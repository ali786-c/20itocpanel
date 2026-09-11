<?php

namespace App\Enums;

enum JobStatus: string
{
    case DRAFT = 'draft';
    case DISCOVERING = 'discovering';
    case AWAITING_MAPPING = 'awaiting_mapping';
    case PREFLIGHT = 'preflight';
    case READY = 'ready';
    case PACKAGING = 'packaging';
    case TRANSFERRING = 'transferring';
    case RESTORING = 'restoring';
    case AWAITING_FINAL_SYNC = 'awaiting_final_sync';
    case FINAL_SYNC = 'final_sync';
    case VERIFYING_FINAL = 'verifying_final';
    case AWAITING_CUTOVER = 'awaiting_cutover';
    case CUTTING_OVER = 'cutting_over';
    case MONITORING = 'monitoring';
    case COMPLETED = 'completed';
    case COMPLETED_WITH_WARNINGS = 'completed_with_warnings';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    case ROLLED_BACK = 'rolled_back';
}
