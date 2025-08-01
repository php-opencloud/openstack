<?php declare(strict_types=1);

namespace OpenStack\BlockStorage\v2;

/**
 * Represents common constants.
 *
 * @package OpenStack\BlockStorage\v2
 */
abstract class Enum
{
    const STATUS_AVAILABLE = 'available';
    const STATUS_CREATING = 'creating';
    const STATUS_RESERVED = 'reserved';
    const STATUS_ATTACHING = 'attaching';
    const STATUS_DETACHING = 'detaching';
    const STATUS_IN_USE = 'in-use';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_DELETING = 'deleting';
    const STATUS_AWAITING_TRANSFER = 'awaiting-transfer';
    const STATUS_ERROR = 'error';
    const STATUS_ERROR_DELETING = 'error_deleting';
    const STATUS_BACKING_UP = 'backing-up';
    const STATUS_ERROR_BACKING_UP = 'error_backing-up';
    const STATUS_ERROR_RESTORING = 'error_restoring';
    const STATUS_DOWNLOADING = 'downloading';
    const STATUS_UPLOADING = 'uploading';
    const STATUS_RETYPINGi = 'retyping';
    const STATUS_EXTENDING = 'extending';

    const ATTACH_STATUS_ATTACHED = 'attached';
    const ATTACH_STATUS_DETACHED = 'detached';
}
