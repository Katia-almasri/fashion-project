<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class expertNotification
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    
     public $user_id;
     public $company_id;
     public $expert_id;
     public $title;
     public $details;
     public $date;
     public $time;

    public function __construct($data)
    { 
        if($data['user_id']!=null)
            $this->user_id = $data['user_id'];
        if($data['company_id']!=null)
            $this->company_id = $data['company_id'];
        if($data['title']!=null)
            $this->title = $data['title'];
        if($data['details']!=null)
            $this->details = $data['details'];
        if($data['expert_id']!=null)
            $this->details = $data['expert_id'];

        $this->date = date("Y-m-d", strtotime(Carbon::now()));
        $this->time = date("h:i A", strtotime(Carbon::now()));
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['expert-notification'];
    }

    public function broadcastAs()
  {
      return 'expert-notification';
  }
}
