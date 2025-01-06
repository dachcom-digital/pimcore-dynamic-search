<?php

namespace DynamicSearchBundle;

final class DynamicSearchEvents
{
    public const ERROR_DISPATCH_CRITICAL = 'ds.error.critical';
    public const ERROR_DISPATCH_ABORT = 'ds.error.abort';
    public const NEW_DATA_AVAILABLE = 'ds.data.new';
    public const RESOURCE_CANDIDATE_VALIDATION = 'ds.data.resource.validation';
}
