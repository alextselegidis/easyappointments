<?php

namespace Jsvrcek\ICS;

class CalendarStream
{
    //length of line in bytes
    const LINE_LENGTH = 70;
    
    /**
     * echo items as they are set rather than building stream in memory, for use with streaming a large calendar
     *
     * @var boolean
     */
    private $doImmediateOutput = false;
    
    /**
     *
     * @var string
     */
    private $stream = '';

    /**
     * @param boolean $doImmediateOutput
     */
    public function setDoImmediateOutput($doImmediateOutput)
    {
        $this->doImmediateOutput = $doImmediateOutput;
    }
    
    /**
     * resets stream to blank string
     */
    public function reset()
    {
        $this->stream = '';
    }
    
    /**
     * @return string
     */
    public function getStream()
    {
        return $this->stream;
    }
    
    /**
     * splits item into new lines if necessary
     * @param string $item
     * @return CalendarStream
     */
    public function addItem($item)
    {
        // newlines need to be converted to literal \n
        $item = str_replace("\n", "\\n", str_replace("\r\n", "\n", $item));
        
        //get number of bytes
        $length = strlen($item);
        
        $block = '';
        
        if ($length > 75) {
            $start = 0;
            
            while ($start < $length) {
                $cut = mb_strcut($item, $start, self::LINE_LENGTH, 'UTF-8');
                $block .= $cut;
                $start = $start + strlen($cut);
                
                //add space if not last line
                if ($start < $length) {
                    $block .= Constants::CRLF.' ';
                }
            }
        } else {
            $block = $item;
        }
    
        $this->stream .= $block.Constants::CRLF;
        
        if ($this->doImmediateOutput) {
            echo $this;
            $this->reset();
        }
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getStream();
    }
}
