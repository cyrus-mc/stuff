#!/bin/bash

# grab the window ID using xwininfo
windowid=$(xwininfo -name "xclock" | grep '"xclock"' | awk '{ print $4 }')

sleep 5

for n in `seq 10 69`; do
	import -frame -window $windowid clock$n.gif &
	sleep 1s
done

convert -resize 50% -loop 0 -delay 100 clock?[0-9].gif clocktick.gif
