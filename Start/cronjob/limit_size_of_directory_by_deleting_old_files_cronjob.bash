#!/bin/bash

# limit the size of folder to 44G
cd /videos && \
ls -ltc | awk \
'{ if (!system("test -f " $9)) { size += $5; if (size > 44*2^30 ) system("rm " $9) } }'

# */10 * * * * /bin/bash /cronjob/limit_size_of_directory_by_deleting_old_files_cronjob.bash