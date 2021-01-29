<script>
    var ws_url = 'wss://' + "{{ request()->getHost() }}" + '/wss/';
    var global_peer_id;
    var rtc_configuration = {iceServers: [{urls: "stun:stun.l.google.com:19302"}]};
    var default_constraints = {video: true, audio: true};

    var connect_attempts = 0;
    var peer_connection;
    var send_channel;
    var ws_conn;
    var local_stream_promise;
    var file_name;
    var file_size;

    let receiveBuffer = [];
    let receivedSize = 0;

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
        $.post('/api/videos/token', {token: global_peer_id, file_name: file_name, _token: $('meta[name=csrf-token]').attr('content')}, function(response){
            let data = JSON.parse(response)
            file_size = data.file_size
        })
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
            case "HELLO": // STEP 1 open WebSocket
                setStatus("Registered with server, waiting for call");
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
                if (!peer_connection)
                    createCall(msg); // STEP 2 peer informs to initiate WebRTC

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

    function websocketServerConnect(fileName, fileSize) {
        file_name = fileName
        file_size = fileSize
        console.log(file_name, file_size)
        return;
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
            getVideoElement().srcObject = event.streams[0];
        }
    }

    function errorUserMediaHandler() {
        setError("Browser doesn't support getUserMedia!");
    }

    const handleDataChannelOpen = (event) =>{
        console.log("dataChannel.OnOpen", event);
    };

    const handleDataChannelMessageReceived = (event) =>{
        // STEP 3 receive WebRTC datachannel message
        console.log("dataChannel.OnMessage:", event, event.data.type);

        setStatus("Received data channel message");
        if (typeof event.data === 'string' || event.data instanceof String) {
            console.log('Incoming string message: ' + event.data);
        } else {
            console.log('Incoming data message');
            receiveBuffer.push(event.data);
            receivedSize += event.data.byteLength;
            console.log('RECEIVED SIZE', receivedSize)
            if (receivedSize === parseInt(file_size)) {
                const received = new Blob(receiveBuffer);
                receiveBuffer = [];

                // STEP 4 file downloaded
                document.querySelector('a#save-file-btn').href = URL.createObjectURL(received);
                document.querySelector('a#save-file-btn').download = file_name;
                $('.downloading').addClass('d-none');
                $('.downloaded').removeClass('d-none');
            }
        }
        send_channel.send("Hi! (from browser)");
    };

    const handleDataChannelError = (error) =>{
        console.log("dataChannel.OnError:", error);
    };

    const handleDataChannelClose = (event) =>{
        console.log("dataChannel.OnClose", event);
    };

    function onDataChannel(event) {
        setStatus("Data channel created");
        let receiveChannel = event.channel;
        receiveChannel.onopen = handleDataChannelOpen;
        receiveChannel.onmessage = handleDataChannelMessageReceived;
        receiveChannel.onerror = handleDataChannelError;
        receiveChannel.onclose = handleDataChannelClose;
    }

    function createCall(msg) {
        // Reset connection attempts because we connected successfully
        connect_attempts = 0;

        console.log('Creating RTCPeerConnection');

        peer_connection = new RTCPeerConnection(rtc_configuration);
        send_channel = peer_connection.createDataChannel('label', null);
        send_channel.binaryType = "arraybuffer";
        send_channel.onopen = handleDataChannelOpen;
        send_channel.onmessage = handleDataChannelMessageReceived;
        send_channel.onerror = handleDataChannelError;
        send_channel.onclose = handleDataChannelClose;
        peer_connection.ondatachannel = onDataChannel;
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