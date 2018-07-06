<?php
/**
 *
 */

defined ('ABSPATH') or die('Permission denied');

class PledgeModel{

    private $pledgePosted = null;
    private $dbTable = 'wp_ths_pledges';
    private $dbFormat = array('%d', '%s', '%s', '%f', '%f', '%s', '%s', '%s', '%s');//format follows db order: userid, name, description, hours, money, currency, zip_code, country, country_code

    public function __construct($pledgePosted = null){
        $this->pledgePosted = $pledgePosted;

    }

    public function validate(){
        if(!$this->isPledgePosted()){
            //@todo - decide how to better handle this error
            die('Error in pledges model - nothing to validate');
        }

        unset($this->pledgePosted['PledgeSubmit']);
        unset($this->pledgePosted['action']);


        $this->validateField('numeric', 'hours');
        $this->validateField('numeric', 'money');
        $this->validateField('string', 'name');
        $this->validateField('string', 'description');
        $this->validateField('string', 'currency');
        $this->validateField('string', 'zip_code');
        $this->validateField('string', 'country');
        $this->validateField('string', 'country_code');

    }

    public function save(){
        if(!$this->isPledgePosted()){
            //@todo - decide how to better handle this error
            die('Error in pledges model - nothing to validate');
        }
        global $wpdb;
        $pledgeData = array(
            'userid'=> get_current_user_id(),
            'name'  => $this->pledgePosted['name'],
            'description'  => $this->pledgePosted['description'],
            'hours'  => $this->pledgePosted['hours'],
            'money'  => $this->pledgePosted['money'],
            'currency'  => 'USD', //@todo $this->pledgePosted['currency'],
            'zip_code'  => $this->pledgePosted['zip_code'],
            'country'  => $this->pledgePosted['country'],
            'country_code'  => $this->pledgePosted['country_code']
        );
        return $wpdb->insert($this->dbTable, $pledgeData, $this->dbFormat);

    }

    public function getAllData(){
        global $wpdb;
        $data = $wpdb->get_results('SELECT * from '. $this->dbTable);//@todo order in any way?
        return $this->sanitizeData($data);

    }

    public function getDataByCountry($country){
        global $wpdb;
        $data = $wpdb->get_results("SELECT * from $this->dbTable WHERE country='$country'");//@todo order in any way?
        return $this->sanitizeData($data);

    }

    public function getDataByZipCode($zipcode){
        global $wpdb;
        $data = $wpdb->get_results("SELECT * from $this->dbTable WHERE zip_code='$zipcode'");//@todo order in any way?
        return $this->sanitizeData($data);

    }

    public function getDataByUserid($userid){
        global $wpdb;
        $data = $wpdb->get_results("SELECT * from $this->dbTable WHERE userid='$userid'");//@todo order in any way?
        return $this->sanitizeData($data);

    }

    private function validateField($type, $name){
        //@todo refactor this crap. It is not readable, it is not easy to maintain, is not solid.

        if(!isset($this->pledgePosted[$name])){
            $this->pledgePosted[$name] = null;
        }

        /*
         * Adds default value as 0 if it comes empty.
         * Dies with an error if user managed to send here a non numeric string.
         */
        if($type == 'numeric'){
            if(!is_numeric($this->pledgePosted[$name])){
                if($this->pledgePosted[$name] == ''){
                    $this->pledgePosted[$name] = 0; //0 is default value

                }else{
                    //@todo show error instead of killing it
                    die('ERROR VALIDATING PLEDGE - Number field is no number');

                }

            }else{
                $this->pledgePosted[$name] = floatval($this->pledgePosted[$name]);

            }
        }

        /*
         * Filter against code injections
         */
        if($type == 'string'){
            $this->pledgePosted[$name] = wp_filter_post_kses($this->pledgePosted[$name]);

        }

    }

    private function sanitizeData($pledgesData){
        //@todo sanitize data for apple maps and html output
        return $pledgesData;

    }

    private function isPledgePosted(){
        return $this->pledgePosted != null;

    }

    public function createDatabase(){
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        //@todo decide if we: DROP TABLE IF EXISTS $this->dbTable;
        $sql = "CREATE TABLE $this->dbTable(
        `pledge_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `userid` bigint(20) unsigned NOT NULL,
          `name` varchar(256) DEFAULT NULL,
          `description` longtext,
          `hours` float DEFAULT NULL,
          `money` float DEFAULT NULL,
          `currency` varchar(256) DEFAULT NULL,
          `zip_code` varchar(256) DEFAULT NULL,
          `country` varchar(256) DEFAULT NULL,
          `country_code` varchar(4) DEFAULT NULL,
          PRIMARY KEY (`pledge_id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=$charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

    }

}