<?php
namespace DTApi\Http\Responses\Common;

class MessageResponse
{
    public MessageType $messageType;

    public string $text;

    public function __construct($messageType, string $text)
    {
        $this->messageType = $messageType;
        $this->text = $text;
    }
}
