# Copyright 2014 Google Inc. All Rights Reserved.
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
"""Command for listing persistent disks."""

from googlecloudsdk.api_lib.compute import base_classes
from googlecloudsdk.api_lib.compute import lister
from googlecloudsdk.api_lib.compute import utils
from googlecloudsdk.calliope import base
from googlecloudsdk.command_lib.compute.disks import flags


@base.ReleaseTracks(base.ReleaseTrack.BETA, base.ReleaseTrack.GA)
class List(base.ListCommand):
  """List Google Compute Engine persistent disks."""

  @staticmethod
  def Args(parser):
    parser.display_info.AddFormat(flags.DEFAULT_LIST_FORMAT)
    parser.display_info.AddUriFunc(utils.MakeGetUriFunc())
    lister.AddZonalListerArgs(parser)

  def Run(self, args):
    holder = base_classes.ComputeApiHolder(self.ReleaseTrack())
    client = holder.client

    request_data = lister.ParseZonalFlags(args, holder.resources)

    list_implementation = lister.ZonalLister(
        client, client.apitools_client.disks)

    return lister.Invoke(request_data, list_implementation)


@base.ReleaseTracks(base.ReleaseTrack.ALPHA)
class ListAlpha(base_classes.MultiScopeLister):
  """List Google Compute Engine persistent disks."""

  @staticmethod
  def Args(parser):
    base_classes.MultiScopeLister.AddScopeArgs(
        parser,
        scopes=[base_classes.ScopeType.zonal_scope,
                base_classes.ScopeType.regional_scope])
    parser.display_info.AddFormat("""
        table(name,
              location(),
              location_scope(),
              sizeGb,
              type.basename(),
              status)
    """)

  def Collection(self):
    """Override the default collection from the base class."""
    return None

  @property
  def global_service(self):
    """The service used to list global resources."""
    raise NotImplementedError()

  @property
  def regional_service(self):
    """The service used to list regional resources."""
    return self.compute.regionDisks

  @property
  def zonal_service(self):
    """The service used to list regional resources."""
    return self.compute.disks

  @property
  def aggregation_service(self):
    """The service used to get aggregated list of resources."""
    return self.zonal_service


List.detailed_help = base_classes.GetZonalListerHelp('disks')
ListAlpha.detailed_help = base_classes.GetMultiScopeListerHelp(
    'disks',
    scopes=[base_classes.ScopeType.zonal_scope,
            base_classes.ScopeType.regional_scope])
