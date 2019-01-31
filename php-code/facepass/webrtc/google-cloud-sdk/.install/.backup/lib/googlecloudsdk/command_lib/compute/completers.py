# Copyright 2017 Google Inc. All Rights Reserved.
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#    http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.

"""Compute resource completers for the core.cache.completion_cache module."""

import os

from googlecloudsdk.command_lib.resource_manager import completers as resource_manager_completers
from googlecloudsdk.command_lib.util import completers
from googlecloudsdk.command_lib.util import parameter_info_lib
from googlecloudsdk.core import exceptions


class Error(exceptions.Error):
  """Exceptions for this module."""


class TestParametersRequired(Error):
  """Test parameters must be exported in _ARGCOMPLETE_TEST."""


# resource param project aggregators


class ResourceParamCompleter(completers.ResourceParamCompleter):

  def ParameterInfo(self, parsed_args, argument):
    return parameter_info_lib.ParameterInfoByConvention(
        parsed_args,
        argument,
        self.collection,
        updaters={
            'project': (resource_manager_completers.ProjectCompleter, True),
        },
    )


# common parameter completers


class RegionsCompleter(ResourceParamCompleter):
  """The region completer."""

  def __init__(self, **kwargs):
    super(RegionsCompleter, self).__init__(
        collection='compute.regions',
        list_command='compute regions list --uri',
        param='region',
        timeout=8*60*60,
        **kwargs)


class ZonesCompleter(ResourceParamCompleter):
  """The zone completer."""

  def __init__(self, **kwargs):
    super(ZonesCompleter, self).__init__(
        collection='compute.zones',
        list_command='compute zones list --uri',
        param='zone',
        timeout=8*60*60,
        **kwargs)


# completers by parameter name convention


COMPLETERS_BY_CONVENTION = {
    'project': (resource_manager_completers.ProjectCompleter, True),
    'region': (RegionsCompleter, False),
    'zone': (ZonesCompleter, False),
}


# list command project aggregators


class ListCommandCompleter(completers.ListCommandCompleter):

  def ParameterInfo(self, parsed_args, argument):
    return parameter_info_lib.ParameterInfoByConvention(
        parsed_args,
        argument,
        self.collection,
        updaters=COMPLETERS_BY_CONVENTION,
    )


class GlobalListCommandCompleter(ListCommandCompleter):
  """A global resource list command completer."""

  def ParameterInfo(self, parsed_args, argument):
    return parameter_info_lib.ParameterInfoByConvention(
        parsed_args,
        argument,
        self.collection,
        additional_params=['global'],
        updaters=COMPLETERS_BY_CONVENTION,
    )


# completers referenced by multiple command groups and/or flags modules
#
# Deprecated* completers have a non-deprecated ResourceSearchCompleter
# counterpart that will be enabled after the new cache is switched on.


class DisksCompleter(ListCommandCompleter):

  def __init__(self, **kwargs):
    super(DisksCompleter, self).__init__(
        collection='compute.disks',
        list_command='compute disks list --uri',
        **kwargs)


class DiskTypesRegionalCompleter(ListCommandCompleter):

  def __init__(self, **kwargs):
    super(DiskTypesRegionalCompleter, self).__init__(
        collection='compute.regionDiskTypes',
        api_version='alpha',
        list_command='alpha compute disk-types list --uri --filter=-zone:*',
        **kwargs)


class DiskTypesZonalCompleter(ListCommandCompleter):

  def __init__(self, **kwargs):
    super(DiskTypesZonalCompleter, self).__init__(
        collection='compute.diskTypes',
        api_version='alpha',
        list_command='alpha compute disk-types list --uri --filter=zone:*',
        **kwargs)


class DiskTypesCompleter(completers.MultiResourceCompleter):

  def __init__(self, **kwargs):
    super(DiskTypesCompleter, self).__init__(
        completers=[DiskTypesRegionalCompleter, DiskTypesZonalCompleter],
        **kwargs)


class DeprecatedDiskTypesCompleter(ListCommandCompleter):

  def __init__(self, **kwargs):
    super(DeprecatedDiskTypesCompleter, self).__init__(
        collection='compute.diskTypes',
        list_command='compute disk-types list --uri',
        **kwargs)


class HealthChecksCompleter(completers.ResourceSearchCompleter):

  def __init__(self, **kwargs):
    super(HealthChecksCompleter, self).__init__(
        collection='compute.healthChecks',
        **kwargs)


class DeprecatedHealthChecksCompleter(ListCommandCompleter):

  def __init__(self, **kwargs):
    super(DeprecatedHealthChecksCompleter, self).__init__(
        collection='compute.healthChecks',
        list_command='compute health-checks list --uri',
        **kwargs)


class HttpHealthChecksCompleter(completers.ResourceSearchCompleter):

  def __init__(self, **kwargs):
    super(HttpHealthChecksCompleter, self).__init__(
        collection='compute.httpHealthChecks',
        **kwargs)


class DeprecatedHttpHealthChecksCompleter(ListCommandCompleter):

  def __init__(self, **kwargs):
    super(DeprecatedHttpHealthChecksCompleter, self).__init__(
        collection='compute.httpHealthChecks',
        list_command='compute http-health-checks list --uri',
        **kwargs)


class HttpsHealthChecksCompleter(completers.ResourceSearchCompleter):

  def __init__(self, **kwargs):
    super(HttpsHealthChecksCompleter, self).__init__(
        collection='compute.httpsHealthChecks',
        **kwargs)


class DeprecatedHttpsHealthChecksCompleter(ListCommandCompleter):

  def __init__(self, **kwargs):
    super(DeprecatedHttpsHealthChecksCompleter, self).__init__(
        collection='compute.httpsHealthChecks',
        list_command='compute https-health-checks list --uri',
        **kwargs)


class InstancesCompleter(completers.ResourceSearchCompleter):

  def __init__(self, **kwargs):
    super(InstancesCompleter, self).__init__(
        collection='compute.instances',
        **kwargs)


class DeprecatedInstancesCompleter(ListCommandCompleter):

  def __init__(self, **kwargs):
    super(DeprecatedInstancesCompleter, self).__init__(
        collection='compute.instances',
        list_command='compute instances list --uri',
        **kwargs)


class InstanceGroupsCompleter(ListCommandCompleter):

  def __init__(self, **kwargs):
    super(InstanceGroupsCompleter, self).__init__(
        collection='compute.instanceGroups',
        list_command='compute instance-groups unmanaged list --uri',
        **kwargs)


class InstanceTemplatesCompleter(completers.ResourceSearchCompleter):

  def __init__(self, **kwargs):
    super(InstanceTemplatesCompleter, self).__init__(
        collection='compute.instanceTemplates',
        **kwargs)


class DeprecatedInstanceTemplatesCompleter(ListCommandCompleter):

  def __init__(self, **kwargs):
    super(DeprecatedInstanceTemplatesCompleter, self).__init__(
        collection='compute.instanceTemplates',
        list_command='compute instance-templates list --uri',
        **kwargs)


class MachineTypesCompleter(ListCommandCompleter):

  def __init__(self, **kwargs):
    super(MachineTypesCompleter, self).__init__(
        collection='compute.machineTypes',
        list_command='compute machine-types list --uri',
        **kwargs)


class RoutesCompleter(ListCommandCompleter):

  def __init__(self, **kwargs):
    super(RoutesCompleter, self).__init__(
        collection='compute.routes',
        list_command='compute routes list --uri',
        **kwargs)


# completers for testing the completer framework


class TestCompleter(ListCommandCompleter):
  """A completer that checks env var _ARGCOMPLETE_TEST for completer info.

  For testing list command completers.

  The env var is a comma separated list of name=value items:
    collection=COLLECTION
      The collection name.
    list_command=COMMAND
      The gcloud list command string with gcloud omitted.
  """

  def __init__(self, **kwargs):
    test_parameters = os.environ.get('_ARGCOMPLETE_TEST', 'parameters=bad')
    kwargs = dict(kwargs)
    for pair in test_parameters.split(','):
      name, value = pair.split('=')
      kwargs[name] = value
    if 'collection' not in kwargs or 'list_command' not in kwargs:
      raise TestParametersRequired(
          'Specify test completer parameters in the _ARGCOMPLETE_TEST '
          'environment variable. It is a comma-separated separated list of '
          'name=value test parameters and must contain at least '
          '"collection=COLLECTION,list_command=LIST COMMAND" parameters.')
    super(TestCompleter, self).__init__(**kwargs)
