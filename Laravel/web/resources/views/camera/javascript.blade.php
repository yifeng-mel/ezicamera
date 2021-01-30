<script>
    window.onload = websocketServerConnect;

    var ws_url = 'wss://' + "{{ request()->getHost() }}" + '/wss/';
    var global_peer_id;
    var rtc_configuration = {iceServers: [{urls: "stun:stun.l.google.com:19302"}]};
    var default_constraints = {video: true, audio: true};

    var connect_attempts = 0;
    var peer_connection;
    var ws_conn;
    var local_stream_promise;

    let receiveBuffer = [];
    let receivedSize = 0;

    function displayProgressBar() {
        $('#progress-bar-div').css('display', 'block');
    }

    function setProgressBarTime(value) {
        $('#progress-bar').css('transition', value);
    }

    function setProgressBarText(text) {
        $('#progress-bar-text').html(text);
    }

    function setProgressBarWidth(width) {
        $('#progress-bar').width(width);
    }   

    function removeProgressBarDiv() {
        $('#progress-bar-div').remove();
    }

    function updateProgressBar(text, width) {
        setProgressBarText(text)
        setProgressBarWidth(width)
    }

    function generateRandomString(length) {
        var result           = '';
        var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for ( var i = 0; i < length; i++ ) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }

    function generateToken() {
        return generateRandomString(256)
    }

    function postToken() {
        $.post('/camera/token', {token: global_peer_id, _token: $('meta[name=csrf-token]').attr('content')})
    }

    function getOurId() {
        return generateToken()
    }

    function resetState() {
        // This will call onServerClose()
        ws_conn.close();
    }

    function handleIncomingError(error) {
        setError("ERROR: " + error);
        resetState();
    }

    function getVideoElement() {
        return document.getElementById("stream");
    }

    function setStatus(text) {
        console.log(text);
    }

    function setError(text) {
        console.error(text);
    }

    function resetVideo() {
        // Release the webcam and mic
        if (local_stream_promise)
            local_stream_promise.then(stream => {
                if (stream) {
                    stream.getTracks().forEach(function (track) { track.stop(); });
                }
            });

        // Reset the video element and stop showing the last received frame
        var videoElement = getVideoElement();
        videoElement.pause();
        videoElement.src = "";
        videoElement.load();
    }

    // SDP offer received from peer, set remote description and create an answer
    function onIncomingSDP(sdp) {
        peer_connection.setRemoteDescription(sdp).then(() => {
            setStatus("Remote SDP set");
            if (sdp.type != "offer")
                return;
            setStatus("Got SDP offer");
            // local_stream_promise.then((stream) => {
            //     setStatus("Got local stream, creating answer");
            //     peer_connection.createAnswer()
            //     .then(onLocalDescription).catch(setError);
            // }).catch(setError);
            peer_connection.createAnswer()
            .then(onLocalDescription).catch(setError);
        }).catch(function(){
            setStatus(JSON.stringify(sdp))
        });
    }

    // Local description was set, send it to peer
    function onLocalDescription(desc) {
        console.log("Got local description: " + JSON.stringify(desc));
        peer_connection.setLocalDescription(desc).then(function() {
            setStatus("Sending SDP answer");
            sdp = {'sdp': peer_connection.localDescription}
            ws_conn.send(JSON.stringify(sdp));
        });
    }

    // ICE candidate received from peer, add it to the peer connection
    function onIncomingICE(ice) {
        var candidate = new RTCIceCandidate(ice);
        peer_connection.addIceCandidate(candidate).catch(setError);
    }

    function onServerMessage(event) {
        console.log("Received " + event.data);
        switch (event.data) {
            case "HELLO":
                setStatus("Registered with server, waiting for call");
                setProgressBarTime('width 10s ease')
                updateProgressBar('Connecting to camera ...', '90%')
                postToken();
                return;
            default:
                if (event.data.startsWith("ERROR")) {
                    handleIncomingError(event.data);
                    return;
                }
                // Handle incoming JSON SDP and ICE messages
                try {
                    msg = JSON.parse(event.data);
                } catch (e) {
                    if (e instanceof SyntaxError) {
                        handleIncomingError("Error parsing incoming JSON: " + event.data);
                    } else {
                        handleIncomingError("Unknown error parsing response: " + event.data);
                    }
                    return;
                }

                // Incoming JSON signals the beginning of a call
                if (!peer_connection) {
                    createCall(msg);
                }

                if (msg.sdp != null) {
                    onIncomingSDP(msg.sdp);
                } else if (msg.ice != null) {
                    onIncomingICE(msg.ice);
                } else {
                    handleIncomingError("Unknown incoming JSON: " + msg);
                }
        }
    }

    function onServerClose(event) {
        setStatus('Disconnected from server');
        resetVideo();

        if (peer_connection) {
            peer_connection.close();
            peer_connection = null;
        }

        // Reset after a second
        window.setTimeout(websocketServerConnect, 1000);
    }

    function onServerError(event) {
        setError("Unable to connect to server, did you add an exception for the certificate?")
        // Retry after 3 seconds
        window.setTimeout(websocketServerConnect, 3000);
    }

    function websocketServerConnect() {
        displayProgressBar()
        updateProgressBar('Establishing secure connection ...', '20%')
        connect_attempts++;
        if (connect_attempts > 3) {
            setError("Too many connection attempts, aborting. Refresh page to try again");
            return;
        }

        // Fetch the peer id to use
        peer_id = getOurId();
        global_peer_id = peer_id;
        setStatus("Connecting to server " + ws_url);
        ws_conn = new WebSocket(ws_url);
        /* When connected, immediately register with the server */
        ws_conn.addEventListener('open', (event) => {
            ws_conn.send('HELLO ' + peer_id);
            setStatus("Registering with server");
        });
        ws_conn.addEventListener('error', onServerError);
        ws_conn.addEventListener('message', onServerMessage);
        ws_conn.addEventListener('close', onServerClose);
    }

    function onRemoteTrack(event) {
        if (getVideoElement().srcObject !== event.streams[0]) {
            console.log('Incoming stream');
            console.log(event.streams[0]);
            console.log(event)
            console.log('yifeng test')
            setProgressBarTime('width 1.5s ease')
            updateProgressBar('Start streaming ...', '100%')
            
            setTimeout(function(){ 
                removeProgressBarDiv();
                getVideoElement().srcObject = event.streams[0];
            }, 1500);
        }
    }

    function errorUserMediaHandler() {
        setError("Browser doesn't support getUserMedia!");
    }

    function createCall(msg) {
        // Reset connection attempts because we connected successfully
        connect_attempts = 0;

        console.log('Creating RTCPeerConnection');

        peer_connection = new RTCPeerConnection(rtc_configuration);
        peer_connection.ontrack = onRemoteTrack;
        /* Send our video/audio to the other peer */
        // local_stream_promise = getLocalStream().then((stream) => {
        //     console.log('Adding local stream');
        //     peer_connection.addStream(stream);
        //     return stream;
        // }).catch(setError);

        if (!msg.sdp) {
            console.log("WARNING: First message wasn't an SDP message!?");
        }

        peer_connection.onicecandidate = (event) => {
        // We have a candidate, send it to the remote party with the
        // same uuid
        if (event.candidate == null) {
                console.log("ICE Candidate was null, done");
                return;
        }
        ws_conn.send(JSON.stringify({'ice': event.candidate}));
        };

        peer_connection.oniceconnectionstatechange = (event) => {
        console.log(new Date().toLocaleString())
            console.log('onstatechange')
        console.log(peer_connection.iceConnectionState)
            switch(peer_connection.iceConnectionState) {
                case "closed":
                case "failed":
                case "disconnected":
            console.log('creating offer')
                    peer_connection.createOffer({iceRestart:true}).then((offer)=>{
                console.log('creating local description')
                peer_connection.setLocalDescription(offer)
            })
            }
        };

        setStatus("Created peer connection for call, waiting for SDP");
    }

</script>