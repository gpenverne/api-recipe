#!/usr/bin/python
#
import broadlink
import json
import os
import sys

# Command syntax
# broadlink.py [host] [port] [mac] [command]

# Read commands from json
dir_path = os.path.dirname(os.path.realpath(__file__))
file = open(dir_path+'/../../app/config/broadlink/commands.json', 'r')
commands = json.loads(file.read())
file.close()


# Check arguments
availableCommands = {}
for command in commands:
   availableCommands[command['name']] = command

if len(sys.argv) < 5:
    print "Please specify all arguments: broadlink.py [host] [port] [mac_address] [command]"
    sys.exit(0)

if sys.argv[4] not in availableCommands:
    print "Command unknown. Available commands:"
    print ','.join(availableCommands)
    sys.exit(0)


#devices = broadlink.discover(timeout=2)
#device = devices[0]

device = broadlink.rm(host=(sys.argv[1],int(sys.argv[2])), mac=sys.argv[3])
device.auth()

rawCommand = str(availableCommands[sys.argv[1]]['data'])

command = rawCommand.decode('hex')
device.send_data(command)
