<?php

namespace OpenStack\Identity\v3;

class Desc
{
    public static $methods = <<<EOT
An array of authentication methods (in string form) that the SDK will use to authenticate. The only acceptable methods
are "password" or "token".
EOT;

    public static $interface = <<<EOT
Denotes the type of visibility the endpoint will have. Acceptable values are "admin", "public" or "internal". Admin
endpoints are only accessible to users who have authenticated with an admin role. Public endpoints are available to
all users and use a public IP. Internal endpoints are available to all users, but only via an internal, private IP.
EOT;

    public static $region = <<<EOT
Denotes the geographic location that the endpoint will serve traffic from. This provides greater redundancy and also
offers better latency to your regions, but will require the system administrator to set up.
EOT;

    public static $endpointUrl = <<<EOT
The HTTP or HTTPS URL that clients will communicate with when accessing your service endpoint.
EOT;

    public static $projectParent = <<<EOT
The unique ID of the project which serves as the parent for this project. For more information about hierarchical
multitenancy in Keystone v3, see: http://specs.openstack.org/openstack/keystone-specs/specs/juno/hierarchical_multitenancy.html
EOT;

    public static $defaultProject = <<<EOT
The unique ID of the project which will serve as a default for the user. Unless another project ID is specified in an
API operation, it is assumed that this project was meant - and so it is used as a default throughout.
EOT;

    public static $password = <<<EOT
The password for the user that they will use to authenticate with. Please ensure it is sufficiently long and random. If
you want a password generated for you, you can use TODO.
EOT;

    public static $email = <<<EOT
The personal e-mail address of the user
EOT;

    public static $effective = <<<EOT
Use the effective query parameter to list effective assignments at the user, project, and domain level. This parameter
allows for the effects of group membership. The group role assignment entities themselves are not returned in the
collection. This represents the effective role assignments that would be included in a scoped token. You can use the
other query parameters with the effective parameter.

For example, to determine what a user can actually do, issue this request: GET /role_assignments?user.id={user_id}&effective

To return the equivalent set of role assignments that would be included in the token response of a project-scoped
token, issue: GET /role_assignments?user.id={user_id}&scope.project.id={project_id}&effective
EOT;


    public static function id($resource)
    {
        return sprintf("The unique ID, or identifier, for the %s", $resource);
    }

    public static function name($resource)
    {
        return sprintf("The name of the %s", $resource);
    }

    public static function type($resource)
    {
        return sprintf("The type of the %s", $resource);
    }

    public static function desc($resource)
    {
        return sprintf("A human-friendly summary that explains what the %s does", $resource);
    }

    public static function enabled($resource)
    {
        return sprintf(
            "Indicates whether this %s is enabled or not. If not, the %s will be unavailable for use.",
            $resource,
            $resource
        );
    }
}