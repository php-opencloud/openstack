<?php

namespace OpenStack\PublicDns\v2;

enum DnsRecordType: string
{
    case A     = 'a';
    case AAAA  = 'aaaa';
    case CNAME = 'cname';
    case MX    = 'mx';
    case NS    = 'ns';
    case SRV   = 'srv';
    case TXT   = 'txt';
}
