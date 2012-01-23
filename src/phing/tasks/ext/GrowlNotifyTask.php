<?php
/**
 * Copyright (c) 2012, Laurent Laville <pear@laurent-laville.org>
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the authors nor the names of its contributors
 *       may be used to endorse or promote products derived from this software
 *       without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * PHP version 5
 *
 * @category   Tasks
 * @package    Phing
 * @subpackage GrowlNotify
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       https://github.com/llaville/phing-GrowlNotifyTask
 */

require_once 'phing/Task.php';
require_once 'Net/Growl/Autoload.php';

/**
 * Growl notification task for Phing, the PHP build tool.
 *
 * PHP version 5
 *
 * @category   Tasks
 * @package    Phing
 * @subpackage GrowlNotify
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       https://github.com/llaville/phing-GrowlNotifyTask
 */
class GrowlNotifyTask extends Task
{
    protected $name;
    protected $sticky;
    protected $message;
    protected $title;
    protected $notification;
    protected $appicon;
    protected $setHost;
    protected $password;
    protected $priority;
    protected $protocol;
    protected $port;
    protected $icon;

    /**
     * Initializes task with default options
     */
    public function __construct()
    {
        $this->setTaskName('GrowlNotify');
        $this->setName();
        $this->setSticky(false);
        $this->setMessage('');
        $this->setTitle();
        $this->setNotification();
        $this->setAppicon('');
        $this->setHost('127.0.0.1');
        $this->setPassword('');
        $this->setPriority('normal');
        $this->setProtocol('tcp');
        $this->setIcon('');
    }

    /**
     * Defines the name of the application sending the notification
     *
     * @param string $name (optional) Name of the application
     *                     that appears in your Growl preferences
     *                      Default: "Growl for Phing"
     *
     * @return void
     * @throws BuildException
     */
    public function setName($name = null)
    {
        if (is_null($name)) {
            $name = 'Growl for Phing';
        }

        if (!is_string($name)) {
            throw new BuildException(
                '"name" attribute is invalid.' .
                ' Expect to be a string, actual is type of ' . gettype($name)
            );
        }

        $this->name = $name;
    }

    /**
     * Indicates if the notification should be sticky
     *
     * @param bool $sticky (optional) Notification should be sticky
     *
     * @return void
     * @throws BuildException
     */
    public function setSticky($sticky = true)
    {
        if (!is_bool($sticky)) {
            throw new BuildException(
                '"sticky" attribute is invalid.' .
                ' Expect to be a boolean, actual is type of ' . gettype($sticky)
            );
        }

        $this->sticky = $sticky;
    }

    /**
     * The notification's text is required.
     * Use \n to specify a line break.
     *
     * @param string $message Notification's text
     *
     * @return void
     * @throws BuildException
     */
    public function setMessage($message)
    {
        if (!is_string($message)) {
            throw new BuildException(
                '"message" attribute is invalid.' .
                ' Expect to be a string, actual is type of ' . gettype($message)
            );
        }

        $this->message = $message;
    }

    /**
     * The notification's title.
     * Use \n to specify a line break.
     *
     * @param string $title (optional) Notification's title
     *                      Default: GrowlNotify
     *
     * @return void
     * @throws BuildException
     */
    public function setTitle($title = null)
    {
        if (is_null($title)) {
            $title = 'GrowlNotify';
        }

        if (!is_string($title)) {
            throw new BuildException(
                '"title" attribute is invalid.' .
                ' Expect to be a string, actual is type of ' . gettype($title)
            );
        }

        $this->title = $title;
    }

    /**
     * The notification name/type
     *
     * @param string $notification Name/type
     *                             Default: "General Notification"
     *
     * @return void
     * @throws BuildException
     */
    public function setNotification($notification = null)
    {
        if (is_null($notification)) {
            $notification = 'General Notification';
        }

        if (!is_string($notification)) {
            throw new BuildException(
                '"notification" attribute is invalid.' .
                ' Expect to be a string, actual is type of ' . gettype($notification)
            );
        }

        $this->notification = $notification;
    }

    /**
     * The icon of the application being registered.
     *
     * Must be a valid file type (png, jpg, gif, ico).
     * Can be any of the following:
     *  - absolute url (http://domain/image.png)
     *  - absolute file path (c:\temp\image.png)
     *  - relative file path (.\folder\image.png) (relative file paths must start
     *    with a dot and are relative to GrowlNotify's phing task location
     *
     * @param string $icon Icon of the application
     *
     * @return void
     * @throws BuildException
     */
    public function setAppicon($icon)
    {
        if (!is_string($icon)) {
            throw new BuildException(
                '"appicon" attribute is invalid.' .
                ' Expect to be a string, actual is type of ' . gettype($icon)
            );
        }

        // relative location
        if (strpos($icon, '..') === 0) {
            $icon = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . $icon);
        }
        elseif (strpos($icon, '.') === 0) {
            $icon = dirname(__FILE__) . substr($icon, 1);
        }

        $this->appicon = $icon;
    }

    /**
     * The host address to send the notification to.
     *
     * If any value other than 'localhost' or '127.0.0.1' is provided, the
     * host is considered a remote host and the "pass" attribute must also be provided.
     * Default: 127.0.0.1
     *
     * @param string $host Remote host name/ip
     *
     * @return void
     * @throws BuildException
     */
    public function setHost($host)
    {
        if (!is_string($host)) {
            throw new BuildException(
                '"host" attribute is invalid.' .
                ' Expect to be a string, actual is type of ' . gettype($host)
            );
        }

        $this->host = $host;
    }

    /**
     * The password required to send notifications.
     *
     * A password is required to send a request to a remote host. If host attribute
     * is specified and is any value other than 'localhost' or '127.0.0.1',
     * then "pass" attribute is also required.
     * Default: no password
     *
     * @param string $password Password to send request to a remote host
     *
     * @return void
     * @throws BuildException
     */
    public function setPassword($password)
    {
        if (!is_string($password)) {
            throw new BuildException(
                '"password" attribute is invalid.' .
                ' Expect to be a string, actual is type of ' . gettype($password)
            );
        }

        $this->password = $password;
    }

    /**
     * The notification priority.
     *
     * Valid values are : low, moderate, normal, high, emergency
     * Default: normal
     *
     * @param string $priority Notification priority
     *
     * @return void
     * @throws BuildException
     */
    public function setPriority($priority)
    {
        switch ($priority) {
            case 'low' :
                $priority = Net_Growl::PRIORITY_LOW;
                break;
            case 'moderate' :
                $priority = Net_Growl::PRIORITY_MODERATE;
                break;
            case 'normal' :
                $priority = Net_Growl::PRIORITY_NORMAL;
                break;
            case 'high' :
                $priority = Net_Growl::PRIORITY_HIGH;
                break;
            case 'emergency' :
                $priority = Net_Growl::PRIORITY_EMERGENCY;
                break;
            default :
                throw new BuildException(
                    '"priority" attribute is invalid.'
                );
        }

        $this->priority = $priority;
    }

    /**
     * The protocol (and port) to send the notification to.
     *
     * With TCP (GNTP) protocol, port is always 23053
     * With UDP protocol, port is always 9887
     * Default: 23053
     *
     * @param string $protocol Protocol to use to send request to remote host
     *
     * @return void
     * @throws BuildException
     */
    public function setProtocol($protocol)
    {
        switch ($protocol) {
            case 'udp' :
                $port = Net_Growl::UDP_PORT;
                break;
            case 'tcp' :
                $port = Net_Growl::GNTP_PORT;
                break;
            default :
                throw new BuildException(
                    '"protocol" attribute is invalid.' .
                    ' Expect to be either udp or tcp.'
                );
        }

        $this->protocol = $protocol;
        $this->port     = $port;
    }

    /**
     * The icon to show for the notification.
     *
     * Must be a valid file type (png, jpg, gif, ico).
     * Can be any of the following:
     *  - absolute url (http://domain/image.png)
     *  - absolute file path (c:\temp\image.png)
     *  - relative file path (.\folder\image.png) (relative file paths must start
     *    with a dot and are relative to GrowlNotify's phing task location
     *
     * @param string $icon Icon of the message
     *
     * @return void
     * @throws BuildException
     */
    public function setIcon($icon)
    {
        if (!is_string($icon)) {
            throw new BuildException(
                '"icon" attribute is invalid.' .
                ' Expect to be a string, actual is type of ' . gettype($icon)
            );
        }

        // relative location
        if (strpos($icon, '..') === 0) {
            $icon = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . $icon);
        }
        elseif (strpos($icon, '.') === 0) {
            $icon = dirname(__FILE__) . substr($icon, 1);
        }

        $this->icon = $icon;
    }

    /**
     * The main entry point method
     *
     * @return void
     * @throws BuildException
     */
    public function main()
    {
        if (empty($this->message)) {
            throw new BuildException(
                '"message" attribute is required'
            );
        }

        $notifications = array(
            $this->notification
        );
        $options  = array(
            'host'     => $this->host,
            'port'     => $this->port,
            'protocol' => $this->protocol,
        );
        if (!empty($this->appicon)) {
            $options['AppIcon'] = $this->appicon;
        }

        try {
            $growl = Net_Growl::singleton(
                $this->name, $notifications, $this->password, $options
            );
            $response = $growl->register();

            if ($response->getStatus() != 'OK') {
                throw new BuildException(
                    'Growl Error ' . $response->getErrorCode() .
                    ' - ' . $response->getErrorDescription()
                );
            }

            $options = array(
                'sticky'   => $this->sticky,
                'priority' => $this->priority,
                'icon'     => $this->icon,
            );
            $response = $growl->notify(
                $this->notification, $this->title, $this->message, $options
            );

            if ($response->getStatus() != 'OK') {
                throw new BuildException(
                    'Growl Error ' . $response->getErrorCode() .
                    ' - ' . $response->getErrorDescription()
                );
            }

        } catch (Net_Growl_Exception $e) {
            throw new BuildException(
                'Growl Exception : ' . $e->getMessage()
            );
        }
    }

}