#!/bin/bash

# append crontab
# http://serverfault.com/a/296951/331028
(crontab -l ; echo "0 5 * * 1 echo 'hello world'") | crontab -
