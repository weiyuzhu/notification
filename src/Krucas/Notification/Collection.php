<?php namespace Krucas\Notification;

use Illuminate\Support\Contracts\ArrayableInterface;
use Illuminate\Support\Contracts\JsonableInterface;
use Illuminate\Support\Contracts\RenderableInterface;
use Illuminate\Support\Collection as BaseCollection;
use Krucas\Notification\Message;
use Session;

class Collection extends BaseCollection implements RenderableInterface
{
    /**
     * Add message to collection.
     *
     * @param Message $message
     * @return \Krucas\Notification\Collection
     */
    public function add(Message $message)
    {
        $this->items[] = $message;

        return $this;
    }

    /**
     * Adds message to collection only if it is unique.
     *
     * @param Message $message
     * @return \Krucas\Notification\Collection
     */
    public function addUnique(Message $message)
    {
        if(!$this->contains($message))
        {
            return $this->add($message);
        }

        return $this;
    }

    /**
     * Determines if given message is already in collection.
     *
     * @param Message $message
     * @return bool
     */
    public function contains(Message $message)
    {
        return in_array($message, $this->items);
    }

    /**
     * Sets item at given position.
     *
     * @param $position
     * @param \Krucas\Notification\Message $message
     * @return \Krucas\Notification\Collection
     */
    public function setAtPosition($position, Message $message)
    {
        $slicePosition = array_count_before_key($this->items, $position);

        $tmp = array_slice($this->items, $slicePosition, null, true);

        array_splice($this->items, $slicePosition);

        array_set($this->items, $position, $message);

        foreach($tmp as $key => $item)
        {
            $i = $key;
            while(array_key_exists($i, $this->items))
            {
                $i++;
            }
            $this->items[$i] = $item;
        }

        ksort($this->items);

        return $this;
    }

    /**
     * Returns item on a given position.
     *
     * @param $position
     * @return \Krucas\Notification\Message
     */
    public function getAtPosition($position)
    {
        return $this->offsetGet($position);
    }

    /**
     * Returns index value of a given message.
     *
     * @param Message $message
     * @return bool|int
     */
    public function indexOf(Message $message)
    {
        foreach($this as $index => $m)
        {
            if($message === $m)
            {
                return $index;
            }
        }

        return false;
    }

    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     */
    public function render()
    {
        $output = '';

        foreach($this->items as $message)
        {
            $output .= $message->render();
        }

        return $output;
    }

    /**
     * Convert the collection to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}