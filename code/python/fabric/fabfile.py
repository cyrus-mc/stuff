from fabric.api import *
from fabric.colors import red,green
from fabric.decorators import parallel
import requests
import json
import yaml

# disable insecure warnings when connecting to self signed certificates
requests.packages.urllib3.disable_warnings()

class GetOutOfLoop( Exception ) :
  pass

def _get_host_list(user, password, param_name, param_value):
  host_list = []
  headers = { 'Accept': 'application/json' }

  # assumption is that parameters are set on hostgroup
  # that way you don't have to filter through all the hosts (an expensive operation)

  # get all hostgroups
  r = requests.get('https://pit-foreman-01.smarshinc.com/api/hostgroups?per_page=100', headers = headers, auth=(user, password), verify=False)
  hostgroups = json.loads(r.text)

  # loop over results
  hostgroup_id = 0
  try:
    for hostgroup in hostgroups['results']:
      # now query each host group and check the parameters for the one we want
      # the assumption is that the parameter is unique to the hostgroup
      r = requests.get("https://pit-foreman-01.smarshinc.com/api/hostgroups/%(id)s" % { 'id': hostgroup['id'] }, headers=headers, auth=(user, password), verify=False)
      hostgroup_detail = json.loads(r.text)

      # check the parameters
      for parameter in hostgroup_detail['parameters']:
        if parameter.get('name') == param_name and parameter.get('value') == param_value:
          hostgroup_id = hostgroup['id']

          # we got our hostgroup ID, now break out of the loop(s)
          raise GetOutOfLoop
  except GetOutOfLoop:
    pass

  # with hostgroup ID in hand, find all hosts that belong to that hostgroup
  r = requests.get("https://pit-foreman-01.smarshinc.com/api/hosts?per_page=1000&search=hostgroup_id=%(id)s" % { 'id': hostgroup_id }, headers=headers, auth=(user, password), verify=False)
  
  hosts = json.loads(r.text)
  # loop over results and add hostname to host_list
  for host in hosts['results']:
    host_list.append(host['name'])

  return host_list

def hello_world():
  print "HERE"
  #run("echo Hello World")

@parallel(pool_size=5)
def check_module(name):
  with settings(
      hide('warnings', 'running', 'stdout', 'stderr'),
      warn_only=True):

    hostname = env.host_string

    result = run("lsmod | grep " + name)

    if result.return_code == 0:
      print red(hostname) + " : module is installed"
    else:
      print red(hostname) + " : module is not installed"

    return result.return_code

@parallel
def disable_acpi_pad():
  with settings(
      hide('warnings', 'running', 'stdout', 'stderr'),
      warn_only=True):

    hostname = env.host_string

    result = sudo("rmmod -f acpi_pad")

    if result.return_code == 0:
      print red(hostname) + " : module acpi_pad removed"
    else:
      print red(hostname) + " : module acpid_pad failed to be removed"

@parallel
def disable_DynamicCoreAllocation():
  with settings(
      hide('warnings', 'running', 'stdout', 'stderr'),
      warn_only=True):

    hostname = env.host_string

    result = sudo("/opt/dell/toolkit/bin/syscfg --DynamicCoreAllocation=Disabled")

    if result.return_code == 0:
      print red(hostname) + " : successfully disabled DynamicCoreAllocation"
    else:
      print red(hostname) + " : unable to disable DynamicCoreAllocation"

def run_all():
  # check if acpi_pad module is present
  disable_acpi_pad()

  # check if DynamicCoreAllocation is enabled, and disable if so
  disable_DynamicCoreAllocation()

if __name__ != '__main__':

  # set your foreman credentials here
  foreman_user = 'mceroni'
  foreman_password = 'Hf8jsvol.'

  # set SSH username
  env.user = "mceroni"

  # what hosts to run against
  env.hosts = [ ]

  # query foreman for a list of hosts to run against if filter option is set
  if 'filter' in env:
    key, val = env.filter.split(':')
    env.hosts += _get_host_list(foreman_user, foreman_password, key, val)

  # if an input file is specified, read that and append to current list
  if 'list' in env:
    env.hosts += open(env.list, 'r').readlines()
