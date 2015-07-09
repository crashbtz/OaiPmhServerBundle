# OaiPmhServerBundle

## About

Provides an Oai-Pmh server to serve your data.
This is an Oai-Pmh server only, you have to plug your own data provider.

## Features

* Compliant with official Oai-Pmh tech spec : http://www.openarchives.org/OAI/openarchivesprotocol.html
* Automated resumption in large list, with arrays or ArrayObject
* On fly XML generation, if you provide Records in a real-time data-accesing ArrayObject
* Only supports oai_dc metadata Format until now
* Fix resumption item-per-page at 50 (should be a parameter)

## Installation

Require the `naoned/OaiPmhServer` package in your composer.json and update your dependencies.

    $ composer require naoned/OaiPmhServer:*

Add the NaonedOaiPmhServerBundle to your application's kernel:

```php
    public function registerBundles()
    {
        $bundles = array(
            ...
            new Naoned\OaiPmhServer\NaonedOaiPmhServerBundle(),
            ...
        );
        ...
    }
```

## Configuration

```yml
naoned_oai_pmh_server:
    data_provider_service_name: naoned.oaipmh.data_provider
```

## Define service

In your own Bundle that manage data, add a service to expose data
In file src/[YOUR_VENDOR]/[YOUR_BUNDLE]/Resources/config/services.yml
```yml
    naoned.oaipmh.data_provider:
        class: [YOUR_VENDOR]\[YOUR_BUNDLE]\[YOUR_PATH]\[YOUR_CLASS]
        calls:
            - [ setContainer, [@service_container] ]
```

## Create Data provider

Fournishing data is up to you.
That’s why you have to define a service.
In order to do it, create on your side a class based on this example :

```php

namespace [YOUR_VENDOR]\[YOUR_BUNDLE]\[YOUR_PATH];

use Naoned\OaiPmhServerBundle\DataProvider\DataProviderInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Naoned\VanaoBundle\Search\SearchArrayObject;

class [YOUR_CLASS] extends ContainerAware implements DataProviderInterface
{
    /**
     * @return string Repository name
     */
    public function getRepositoryName()
    {
        return 'My super Oai-Pmh Server';
    }

    /**
     * @return string Repository admin email
     */
    public function getAdminEmail()
    {
        return 'me@home.com';
    }

    /**
     * @return string Repository earliest update change on data
     */
    public function getEarliestDatestamp()
    {
        return "2015-01-01";
    }

    /**
     * @param  string $identifier [description]
     * @return array
     */
    public function getRecord($identifier)
    {
        return array(
            'title'       => 'Dummy content',
            'description' => 'Some more dummy content',
            'sets'        => array('seta', 'setb'),
        );
    }

    /**
     * must return an array of arrays with keys «identifier» and «name»
     * @return array List of all sets, with identifier and name
     */
    public function getSets()
    {
        return array('seta', 'setb', 'setc');
    }

    /**
     * Search for records
     * @param  String|null    $setTitle Title of wanted set
     * @param  \DateTime|null $from     Date of last change «from»
     * @param  \DataTime|null $until    Date of last change «until»
     * @return array|ArrayObject        List of items
     */
    public function getRecords($setTitle = null, \DateTime $from = null, \DataTime $until = null)
    {
        return array(
            array(
                'title'       => 'Dummy content 1',
                'description' => 'Some more dummy content',
                'sets'        => array('seta', 'setb'),
            ),
            array(
                'title'       => 'Dummy content 2',
                'description' => 'Some more dummy content',
                'sets'        => array('seta'),
            ),
            array(
                'title'       => 'Dummy content 3',
                'description' => 'Some more dummy content',
                'sets'        => array('seta'),
            ),
            array(
                'title'       => 'Dummy content 4',
                'description' => 'Some more dummy content',
                'sets'        => array('setc'),
            ),
            array(
                'title'       => 'Dummy content 5',
                'description' => 'Some more dummy content',
                'sets'        => array('setd'),
            ),
        );
    }

    /**
     * Tell me, this «record», in which «set is it ?
     * @param  any   $record An item of elements furnished by getRecords method
     * @return array         List of sets, the record belong to
     */
    public function getSetsForRecord($record)
    {
        return $record['sets'];
    }

    /**
     * Transform the provided record in an array with Dublin Core, «dc_title»  style
     * @param  any   $record An item of elements furnished by getRecords method
     * @return array         Dublin core data
     */
    public static function dublinizeRecord($record)
    {
        return array(
            'dc_title'       => $record['title'],
            'dc_description' => $record['description'],
        );
    }

    /**
     * Check if sets are supported by data provider
     * @return boolean check
     */
    public function checkSupportSets()
    {
        return true;
    }
}

```

Of course, you have to implement data retreiveing here, based on anything : db (Sql), mappers (Doctrine, Pomm) or any other data storing (ElasticSearch …). That why I made this class container aware, but you can preferely set required services via setters.

In addition, lists (records ans sets) can be sent as ArrayObjects, in order to manage data calling in an other class that implements ```\ArrayObject```.