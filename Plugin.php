<?php
class EmailNewsletterPlugin extends \Enact\Plugin {



    public function name(){
        return 'EmailNewsletter';
    }//name



    public function creator(){
        return 'WebYoke';
    }//creator


    public function website(){
        return 'http://webyoke.com';
    }//website



    public function version(){
        return 1.0;
    }//version


    public function githubLink(){
        return 'https://github.com/webyoke/enact-cms-email-newsletter-plugin';
    }//githubLink


    public function onInstall(){

        \DB::query('
                CREATE TABLE `' . enact()->config('DB_PREFIX') . 'email_newsletter_email` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `email` VARCHAR(480) NOT NULL,
                `ip_address` VARCHAR(45) NOT NULL,
                `created_on` DATETIME NOT NULL,
                `token` VARCHAR(256) NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE INDEX `email_UNIQUE` (`email` ASC))
            ');

        \DB::query('
                CREATE TABLE `' . enact()->config('DB_PREFIX') . 'email_newsletter_campaign` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `title` VARCHAR(420) NOT NULL,
                `template` VARCHAR(180) NOT NULL,
                `token` VARCHAR(256) NOT NULL,
                `total_sent` INT NOT NULL,
                `total_viewed` INT NOT NULL,
                `total_click_through` INT NOT NULL,
                `created_on` DATETIME NOT NULL,
                `variables` TEXT NULL,
                PRIMARY KEY (`id`));
            ');

        \DB::foreignKeyChecksOff();

        \DB::query('
                CREATE TABLE `' . enact()->config('DB_PREFIX') . 'email_newsletter_campaign_email` (
                `campaign_id` INT NOT NULL,
                `email_id` INT NOT NULL,
                `viewed_on` DATETIME NULL,
                `clicked_through_on` DATETIME NULL,
                PRIMARY KEY (`campaign_id`, `email_id`),
                INDEX `viewed_on` (`viewed_on` ASC),
                INDEX `clicked_through_on` (`clicked_through_on` ASC),
                INDEX `fk_email_newsletter_campaign_email_email_id_idx` (`email_id` ASC),
                CONSTRAINT `fk_email_newsletter_campaign_email_campaign_id`
                FOREIGN KEY (`campaign_id`)
                REFERENCES `ENACTCMS`.`email_newsletter_campaign` (`id`)
                ON DELETE CASCADE
                ON UPDATE NO ACTION,
                CONSTRAINT `fk_email_newsletter_campaign_email_email_id`
                FOREIGN KEY (`email_id`)
                REFERENCES `ENACTCMS`.`email_newsletter_email` (`id`)
                ON DELETE CASCADE
                ON UPDATE NO ACTION);
            ');

        \DB::foreignKeyChecksOn();

    }//onInstall



    public function onUnInstall(){

        \DB::query('DROP TABLE ' . enact()->config('DB_PREFIX') . 'email_newsletter_email');
        \DB::query('DROP TABLE ' . enact()->config('DB_PREFIX') . 'email_newsletter_email_campaign');
        \DB::query('DROP TABLE ' . enact()->config('DB_PREFIX') . 'email_newsletter_email_campaign_email');

    }//onUnInstall



    public function onBoot(){

        require 'vendor/autoload.php';

    }//onBoot



    public function permissions(){
        return Array(
            'Manage Email Campaigns' => 'manage_email_campaigns'
        );
    }//permissions



    public function template(){
        return new EmailNewsletter\Twig;
    }//template



    public function templatePath(){
        return 'app/template';
    }//templatePath



    public function menuItem(){

        if(!enact()->with('User')->hasPermission('manage_email_campaigns')){
            return null;
        }//if

        return Array(
            'title' => 'Email Newsletter',
            'href'  => enact_cpSlug('email-newsletter/'),
            'icon'  => 'fi-book icon',
            'children' => Array(
                Array(
                    'title' => 'Subscriptions',
                    'href'  => enact_cpSlug('email-newsletter/subscriptions/'),
                ),
                Array(
                    'title' => 'Campaigns',
                    'href'  => enact_cpSlug('email-newsletter/campaigns/'),
                )
            )
        );

    }//menuItem



    public function publicRoutes(){
        require 'app/router/public.php';
    }//publicRoutes



    public function privateRoutes(){

        if(!enact()->with('User')->hasPermission('manage_email_campaigns')){
            return null;
        }//if

        require 'app/router/private.php';

    }//privateRoutes




}//EmailNewsletterPlugin

return new EmailNewsletterPlugin;
