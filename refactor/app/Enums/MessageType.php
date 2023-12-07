<?php
namespace DTApi\Enums;

enum MessageType: string
{
    case SUCCESS = "SUCCESS";
    case INFO = "INFO";
    case WARNING  = "WARNING";
    case ERROR = "ERROR";
}