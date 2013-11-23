<?php // $Id$
/*
 * WavForge
 * Copyright (c) sk89q <http://sk89q.therisenrealm.com>
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 * 
 * Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 * 
 * Redistributions in binary form must reproduce the above copyright
 * notice, this list of conditions and the following disclaimer in the
 * documentation and/or other materials provided with the distribution.
 * 
 * Neither the name of sk89q nor the names of its contributors may be
 * used to endorse or promote products derived from this software
 * without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

/**
 * @package com.therisenrealm.sound.sampled
 */

// PHPNS: namespace com\therisenrealm\sound\sampled

/**
 * This class generates PCM WAV files and can also synthesize some
 * waveforms.
 *
 * This is largely a proof of concept. It runs too slowly to be of much
 * use. 
 * 
 * @package com.therisenrealm.sound.sampled
 */
class WavForge
{
	/**
	 * Store the number of channels to be generated.
	 *
	 * @var int
	 */
    private $channels = 1;
    /**
     * The sample rate at which the sample_count will be generated at.
     *
     * @var int
     */
    private $sample_rate = 44100;
    /**
     * Maximum number of bits per sample.
     *
     * @var unknown_type
     */
    private $bits_per_sample = 16;
    
    /**
     * Store the number of samples that have been generated.
     *
     * @var int
     */
    private $sample_count = 0;
    /**
     * Contains the samples.
     *
     * @var string
     */
    private $output;
    
    /**
     * Constructs the object. The number of channels, the sample rate, and
     * the bits per sample can be specified.
     *
     * @param int $channels
     * @param int $sample_rate
     * @param int $bits_per_sample
     */
    public function __construct($channels = 2, $sample_rate = 44100,
                                $bits_per_sample = 16)
    {
    	$this->channels = $channels;
    	$this->sample_rate = $sample_rate;
    	$this->bits_per_sample = $bits_per_sample;
    }
    
    /**
     * Get the number of channels specified.
     *
     * @return int
     */
    public function getChannels()
    {
    	return $this->channels;
    }
    
    /**
     * Specify the number of channels. Once audio data has been generated,
     * the number of channels should not be changed.
     *
     * @param int $channels
     */
    public function setChannels($channels)
    {
    	$this->channels = $channels;
    }
    
    /**
     * Get the sample rate specified.
     *
     * @return int
     */
    public function getSampleRate()
    {
    	return $this->sample_rate;
    }
    
    /**
     * Specify the sample rate. Once audio data has been generated, the 
     * sample rate should not be changed.
     *
     * @param int $sample_rate
     */
    public function setSampleRate($sample_rate)
    {
    	$this->sample_rate = $sample_rate;
    }
    
    /**
     * Get the bits per sample specified.
     *
     * @return int
     */
    public function getBitsPerSample()
    {
    	return $this->bits_per_sample;
    }
    
    /**
     * Specify the bits per sample. Once audio data has been generated, do
     * not change the bits per sample.
     *
     * @param int $bits_per_sample
     */
    public function setBitsPerSample($bits_per_sample)
    {
    	$this->bits_per_sample = $bits_per_sample;
    }
    
    /**
     * Get the count of samples.
     *
     */
    public function getSampleCount()
    {
    	return $this->sample_count;
    }
    
    /**
     * Get the raw sample data.
     *
     * @return string
     */
    public function getData()
    {
    	return $this->output;
    }
    
    /**
     * Add samples.
     *
     * @param string $data
     * @param int $sample_count
     */
    public function addSamples($data, $sample_count)
    {
    	$this->data .= $data;
    	$this->sample_count += $sample_count;
    }
    
    /**
     * Gets the WAV file with the data and header.
     *
     * @return string
     */
    public function getWAVData()
    {
        return $this->getWAVHeader() . $this->output;
    }
    
    /**
     * Generate the WAV header.
     *
     * @return string
     */
    private function getWAVHeader()
    {
        $subchunk_2_size = $this->sample_count * $this->channels * 
            $this->bits_per_sample / 8;
        
        $header .= pack('N', 0x52494646); // ChunkID [0,4] RIFF
        $header .= pack('V', $subchunk_2_size + 36); // ChunkSize [0,4]
        $header .= pack('N', 0x57415645); // Format [8,4] WAVE
        $header .= pack('N', 0x666d7420); // Subchunk1ID [12,4] fmt
        $header .= pack('V', 16); // Subchunk1Size [16,4] 16 for PCM
        $header .= pack('v', 1); // AudioFormat [20,2] 1 for PCM
        $header .= pack('v', $this->channels); // NumChannels [22,2] 1 for mono, 2 for stereo
        $header .= pack('V', $this->sample_rate); // SampleRate [24,4]
        $header .= pack('V', $this->sample_rate * $this->channels * 
            $this->bits_per_sample / 8); // ByteRate [28,4] SampleRate * NumChannels * BitsPerSample / 8
        $header .= pack('v', $this->channels * $this->bits_per_sample / 8); // BlockAlign [32,2] NumChannels * BitsPerSample / 8
        
        $header .= pack('v', $this->bits_per_sample); // BitsPerSample [34,2]
        $header .= pack('N', 0x64617461); // Subchunk1ID [36,4] data
        $header .= pack('V', $subchunk_2_size); // Subchunk2Size [40,4]
        
        return $header;
    }
    
    /**
     * Encodes a sample.
     *
     * @throws OutOfRangeException Overflow
     * @return string
     */
	public function encodeSample($num)
	{
	    $max = pow(2, $this->bits_per_sample);
	    if ($num < 0) {
	        $num += $max;
	    }
	    if ($num >= $max) {
	        throw new OutOfRangeException("Overflow ({$num} won't fit into an {$this->bits_per_sample}-bit integer)");
	    }
	    $b = array();
	    while ($num > 0) {
	        $b[] = chr($num % 256);
	        $num = floor($num / 256);
	    }
	    for ($i = 0; $i < -(-$this->bits_per_sample >> 3) - count($b); $i++) {
	        $b[] = chr(0);
	    }
	    return implode('', $b);
	}
    
    /**
     * Generate a sine waveform.
     *
     * @param float $frequency
     * @param float $volume Percentage in volume (.5 for 50%)
     * @param float $seconds
     * @throws OutOfRangeException Volume out of range
     */
    public function synthesizeSine($frequency, $volume, $seconds) 
    {
        $b = pow(2, $this->bits_per_sample) / 2;
        for ($i = 0; $i < $seconds * $this->sample_rate; $i++) {
            // Add a sample for each channel
            $this->output .= str_repeat(
                $this->encodeSample(
                    $volume * $b * // <- amplitude
                    sin(2 * M_PI * $i * $frequency / $this->sample_rate)
                ), 
                $this->channels);
            $this->sample_count++;
        }
    }

    /**
     * Generate a sine waveform.
     *
     * @param float $frequency
     * @param float $volume Percentage in volume (.5 for 50%)
     * @param float $seconds
     * @throws OutOfRangeException Volume out of range
     */
    public function synthesizeSineMulti($frequency_array, $volume_max, $seconds) 
    {
        $b = pow(2, $this->bits_per_sample) / 2;
		
		 $count = count($frequency_array);
		
		 for ($j = 0; $j < $count; $j++) {
			
			$frequency = $frequency_array[$j];
			
			for ($i = 0; $i < $seconds * $this->sample_rate; $i++) {
				
				 $n = $seconds * $this->sample_rate;
				
				/*
				 if ($i <= ($n / 2)) {
					
				 	 $v = $volume * 2 * ($i / $n);
				 
				 } else {
					
					 $v = $volume * 2 * (1 - ($i / $n));
				 }
				*/
				
				 $volume = $volume_max * sqrt( 1.0 - (pow($i - ($n / 2), 2) / pow(($n / 2), 2)) );
				
				/*
				 $a1 = $n * 0.1;
				 $a2 = $n * 0.9;
				
				 if ($i < $a1) {
				 	
					 $v =  $volume / $a1;
					
				 } else if ($i >= $a1 && $i <= $a2) {
					
					 $v =  $volume;
					
				 } else {
					
					 $v =  ($n - $i) * $volume / ($n - $a2);
				 }
				*/
				
	            // Add a sample for each channel
	            $this->output .= str_repeat(
	                $this->encodeSample(
	                    $volume * $b * // <- amplitude
	                    sin(2 * M_PI * $i * $frequency / $this->sample_rate)
	                ), 
	                $this->channels);
	            $this->sample_count++;
	        }
		 }
    }
    
    /**
     * Generate a sawtooth waveform.
     *
     * @param float $frequency
     * @param float $volume Percentage in volume (.5 for 50%)
     * @param float $seconds
     * @throws OutOfRangeException Volume out of range
     */
    public function synthesizeSawtooth($frequency, $volume, $seconds) 
    {
        $b = pow(2, $this->bits_per_sample) / 2;
        for ($i = 0; $i < $seconds * $this->sample_rate; $i++) {
            // Add a sample for each channel
            $this->output .= str_repeat(
                $this->encodeSample(
                    $volume * $b * // <- amplitude
                    ($i * $frequency / $this->sample_rate - 
                    floor($i * $frequency / $this->sample_rate))
                ), 
                $this->channels);
            $this->sample_count++;
        }
    }
    
    /**
     * Generate noise.
     *
     * @param float $volume Percentage in volume (.5 for 50%)
     * @param float $seconds
     * @throws OutOfRangeException Volume out of range
     */
    public function synthesizeNoise($volume, $seconds) 
    {
        $b = pow(2, $this->bits_per_sample) / 2;
        for ($i = 0; $i < $seconds * $this->sample_rate; $i++) {
            // Add a sample for each channel
            $this->output .= str_repeat(
                $this->encodeSample(
                    rand(0, $volume * $b)
                ), 
                $this->channels);
            $this->sample_count++;
        }
    }
}