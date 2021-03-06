<?php

/**
 * @file controllers/listbuilder/files/GalleyFilesListbuilderHandler.inc.php
 *
 * Copyright (c) 2003-2013 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class GalleyFilesListbuilderHandler
 * @ingroup listbuilder
 *
 * @brief Class for selecting files to add a user to for copyediting.
 */

import('lib.pkp.controllers.listbuilder.files.FilesListbuilderHandler');
// Get access to the submission file constants.
import('lib.pkp.classes.submission.SubmissionFile');

class GalleyFilesListbuilderHandler extends FilesListbuilderHandler {
	/**
	 * Constructor
	 */
	function GalleyFilesListbuilderHandler() {
		parent::FilesListbuilderHandler(SUBMISSION_FILE_PROOF);
	}


	//
	// Implement template methods from PKPHandler
	//
	/**
	 * @see PKPHandler::authorize()
	 */
	function authorize($request, &$args, $roleAssignments) {
		import('classes.security.authorization.GalleyRequiredPolicy');
		$this->addPolicy(new GalleyRequiredPolicy($request, $args));
		return parent::authorize($request, $args, $roleAssignments, WORKFLOW_STAGE_ID_PRODUCTION);
	}


	/**
	 * Configure the grid
	 * @param PKPRequest $request
	 */
	function initialize($request) {
		parent::initialize($request);
		AppLocale::requireComponents(LOCALE_COMPONENT_APP_EDITOR);
		$this->setTitle('editor.submission.selectGalleyFiles');
	}

	//
	// Implement methods from FilesListbuilderHandler
	//
	/**
	 * @see FilesListbuilderHandler::getOptions()
	 */
	function getOptions() {
		$submission =& $this->getAuthorizedContextObject(ASSOC_TYPE_SUBMISSION);
		$galley =& $this->getAuthorizedContextObject(ASSOC_TYPE_GALLEY);

		$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO'); /* @var $submissionFileDao SubmissionFileDAO */
		$submissionFiles =& $submissionFileDao->getLatestRevisionsByAssocId(
				ASSOC_TYPE_GALLEY, $galley->getId(),
				$submission->getId(), $this->getFileStage()
			);
		return parent::getOptions($submissionFiles);
	}

	/**
	 * @see FilesListbuilderHandler::getRequestArgs
	 */
	function getRequestArgs() {
		$galley =& $this->getAuthorizedContextObject(ASSOC_TYPE_GALLEY);
		$args = parent::getRequestArgs();
		$args['articleGalleyId'] = $galley->getId();
		return $args;
	}
}

?>
