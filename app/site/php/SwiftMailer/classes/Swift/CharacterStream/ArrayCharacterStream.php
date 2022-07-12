<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A CharacterStream implementation which stores characters in an internal array.
 * @package Swift
 * @subpackage CharacterStream
 * @author Chris Corbyn
 */
class Swift_CharacterStream_ArrayCharacterStream implements Swift_CharacterStream
{
    /** A map of byte values and their respective characters */
    private static $_charMap;

    /** A map of characters and their derivative byte values */
    private static $_byteMap;

    /** The char reader (lazy-loaded) for the current charset */
    private $_charReader;

    /** A factory for creatiing CharacterReader instances */
    private $_charReaderFactory;

    /** The character set this stream is using */
    private $_charset;

    /** Array of characters */
    private $_array = array();

    /** Size of the array of character */
    private $_array_size = array();

    /** The current character offset in the stream */
    private $_offset = 0;

    /**
     * Create a new CharacterStream with the given $chars, if set.
     * @param Swift_CharacterReaderFactory $factory for loading validators
     * @param string                       $charset used in the stream
     */
    public function __construct(Swift_CharacterReaderFactory $factory, $charset)
    {
        self::_initializeMaps();
        $this->setCharacterReaderFactory($factory);
        $this->setCharacterSet($charset);
    }

    /**
     * Set the character set used in this CharacterStream.
     * @param string $charset
     */
    public function setCharacterSet($charset)
    {
        $this->_charset = $charset;
        $this->_charReader = null;
    }

    /**
     * Set the CharacterReaderFactory for multi charset support.
     * @param Swift_CharacterReaderFactory $factory
     */
    public function setCharacterReaderFactory(Swift_CharacterReaderFactory $factory)
    {
        $this->_charReaderFactory = $factory;
    }

    /**
     * Overwrite this character stream using the byte sequence in the byte stream.
     * @param Swift_OutputByteStream $os output stream to read from
     */
    public function importByteStream(Swift_OutputByteStream $os)
    {
        if (!isset($this->_charReader)) {
            $this->_charReader = $this->_charReaderFactory
                ->getReaderFor($this->_charset);
        }

        $startLength = $this->_charReader->getInitialByteSize();
        while (false !== $bytes = $os->read($startLength)) {
            $c = array();
            for ($i = 0, $len = strlen($bytes); $i < $len; ++$i) {
                $c[] = self::$_byteMap[$bytes[$i]];
            }
            $size = count($c);
            $need =