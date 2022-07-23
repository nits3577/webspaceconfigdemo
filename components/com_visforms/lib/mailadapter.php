<?php
/**
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2021 vi-solutions
 * @since        Joomla 1.6
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

abstract class VisformsMailadapter
{
    protected $form;
    protected $caller;
    protected $resultmailsettings;
    protected $receiptmailsettings;
    protected $customresultmailsettings;
    protected $customreceiptmailsettings;
    protected $prefix;
    protected $formreceiptmailsettings;
    protected $formresultmailsettings;
    public function __construct($form, $caller)
    {
        $this->form = $form;
        $this->caller = $caller;
        $this->prefix = $this->getPrefix();
        $this->setFormReceiptMailSettings();
        $this->setFormResultMailSettings();
        $this->receiptmailsettings = array(
            $this->prefix .'emailreceipt' => '',
            'emailreceiptsubject' => '',
            'emailreceiptfrom' => '',
            'emailreceiptfromname' => '',
            'emailreceipttext' => '',
            'emailreceiptincfield' => '',
            'emailreceipthideemptyfields' => '',
	        'emailreceiptemptycaliszero' => '',
            'emailreceiptincdatarecordid' => '',
            'emailreceiptinccreated' => '',
            'emailreceiptincformtitle' => '',
            'emailreceiptincip' => '',
	        'emailrecipientincfilepath' => '',
            'emailreceiptincfile' => ''
        );
        $this->resultmailsettings = array(
            $this->prefix . 'emailresult' => '',
            'emailfrom' => '',
            'emailfromname' => '',
            'emailto' => '',
            'emailcc' => '',
            'emailbcc' => '',
            'subject' => '',
            'emailresulttext' => '',
            'emailresultincfield' => '',
            'emailresulthideemptyfields' => '',
	        'emailresultemptycaliszero' => '',
            'emailresultincdatarecordid' => '',
            'emailresultinccreated' => '',
            'emailresultincformtitle' => '',
            'emailresultincip' => '',
            'receiptmailaslink' => '',
	        'emailresultincfilepath' => '1',
            'emailresultincfile' => ''
        );
        $this->getCustomreceiptmailsettings();
        $this->getCustomresultmailsettings();
    }

    public static function getInstance($form, $caller)
    {
        switch ($caller)
        {
            case "vfedit" :
                $classname = 'VisformsMailAdapterVfedit';
                $filename = 'vfedit';
                break;
            default:
                $classname = 'VisformsMailAdapterDefault';
                $filename = 'default';
                break;
        }

        if (!class_exists($classname))
        {
            //try to register it
            JLoader::register($classname, dirname(__FILE__) . '/mail/adapter/' . $filename . '.php');
            if (!class_exists($classname))
            {
                //return a default class?
                return false;
            }
        }
        //delegate to the appropriate subclass
        return new $classname($form, $caller);
    }

    abstract public function result();
    abstract public function receipt();
    abstract protected function getPrefix();
    abstract protected function setFormReceiptMailSettings();
    abstract protected function setFormResultMailSettings();
    protected function getCustomreceiptmailsettings()
    {
        $this->customreceiptmailsettings = array();
    }
    protected function getCustomresultmailsettings()
    {
        $this->customresultmailsettings = array();
    }
}