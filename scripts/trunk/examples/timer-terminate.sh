# example of how to run a specified process for a certain time and then terminate
#!/bin/bash

runtime=${1:-10m}

# store PID of script
mypid=$$

# run xclock in the background
xclock &

# store PID of xclock
clockpid=$!

echo "My PID=$mypid. Clock's PID=$clockpid"

# sleep for a specified amount of time
sleep $runtime
kill -s SIGTERM $clockpid

echo "all done"
