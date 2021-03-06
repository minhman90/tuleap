<?php
/**
 * Copyright (c) Enalean, 2017. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Tuleap\Bugzilla\Reference;

use Reference;
use ReferenceManager;
use Tuleap\reference\ReferenceValidator;
use Valid_HTTPURI;

class ReferenceSaver
{
    /**
     * @var Dao
     */
    private $dao;

    /**
     * @var ReferenceValidator
     */
    private $reference_validator;
    /**
     * @var ReferenceRetriever
     */
    private $reference_retriever;
    /**
     * @var ReferenceManager
     */
    private $reference_manager;

    public function __construct(
        Dao $dao,
        ReferenceValidator $reference_validator,
        ReferenceRetriever $reference_retriever,
        ReferenceManager $reference_manager
    ) {
        $this->dao                 = $dao;
        $this->reference_validator = $reference_validator;
        $this->reference_retriever = $reference_retriever;
        $this->reference_manager   = $reference_manager;
    }

    public function save(\Codendi_Request $request)
    {
        $keyword               = $request->get('keyword');
        $server                = trim($request->get('server'));
        $username              = $request->get('username');
        $api_key               = $request->get('api_key');
        $are_followups_private = $request->get('are_follow_up_private');
        $rest_api_url          = $request->get('rest_url');
        $are_followups_private = isset($are_followups_private) ? $are_followups_private : false;

        if (empty($keyword) || empty($server) || empty($username) || empty($api_key)) {
            throw new RequiredFieldEmptyException();
        }

        $this->checkFieldsValidity($keyword, $server, $rest_api_url);
        $this->createReferenceForBugzillaServer($keyword, $server);

        $this->dao->save($keyword, $server, $username, $api_key, $are_followups_private, $rest_api_url);
    }

    private function checkFieldsValidity($keyword, $server, $rest_api_url)
    {
        if (! $this->reference_validator->isValidKeyword($keyword)) {
            throw new KeywordIsInvalidException();
        }

        if ($this->reference_validator->isSystemKeyword($keyword)
            || $this->reference_validator->isReservedKeyword($keyword)
            || $this->reference_retriever->getReferenceByKeyword($keyword) !== null
        ) {
            throw new KeywordIsAlreadyUsedException();
        }

        $this->validateURLs($server, $rest_api_url);
    }

    private function validateURLs($server, $rest_api_url)
    {
        $http_uri_validator = new Valid_HTTPURI();
        if (! $http_uri_validator->validate($server)) {
            throw new ServerIsInvalidException();
        }

        if ($rest_api_url && ! $http_uri_validator->validate($rest_api_url)) {
            throw new RESTURLIsInvalidException();
        }
    }

    public function edit($request)
    {
        $id                    = $request->get('id');
        $server                = trim($request->get('server'));
        $rest_api_url          = trim($request->get('rest_url'));
        $username              = $request->get('username');
        $api_key               = $this->getAPIKeyToStore($id, $request->get('api_key'));
        $are_followups_private = $request->get('are_follow_up_private');
        $are_followups_private = isset($are_followups_private) ? $are_followups_private : false;

        $this->validateURLs($server, $rest_api_url);

        if (empty($server) || empty($username) || empty($api_key)) {
            throw new RequiredFieldEmptyException();
        }

        $this->dao->edit($id, $server, $username, $api_key, $are_followups_private, $rest_api_url);
    }

    private function getAPIKeyToStore($id, $api_key)
    {
        if ($api_key !== "") {
            return $api_key;
        }

        $reference = $this->dao->getReferenceById($id);

        return $reference['api_key'];
    }

    private function createReferenceForBugzillaServer($keyword, $server)
    {
        $reference = new Reference(
            0,
            $keyword,
            'Bugzilla reference',
            $server . '/show_bug.cgi?id=$1',
            'S',
            '',
            'bugzilla',
            '1',
            100
        );

        if (! $this->reference_manager->createSystemReference($reference)) {
            throw new UnableToCreateSystemReferenceException();
        }
    }
}
