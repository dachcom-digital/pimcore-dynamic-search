<?php

namespace DynamicSearchBundle;

final class DynamicSearchEvents
{
    const ERROR_DISPATCH_CRITICAL = 'ds.error.critical';

    const ERROR_DISPATCH_ABORT = 'ds.error.abort';

    const NEW_DATA_AVAILABLE = 'ds.data.new';

    const RESOURCE_CANDIDATE_VALIDATION = 'ds.data.resource.validation';
}
