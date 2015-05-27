<?php

class IndexController extends AuthenticatedApplicationController {

	/**
	 * Returns all User records matching the query. Examples:
	 * GET /users?column=value&order_by=column&dir=DESC&limit=20&page=2&count_only
	 * GET /rest/users.json&limit=5
	 *
	 * @return User[]
	 */
	function index() {
		$q = User::getQuery(@$_GET);

		// paginate
		$limit = empty($_REQUEST['limit']) ? 25 : $_REQUEST['limit'];
		$page = empty($_REQUEST['page']) ? 1 : $_REQUEST['page'];
		$class = 'User';
		$method = 'doSelectIterator';
		$this['pager'] = new QueryPager($q, $limit, $page, $class, $method);

		if (isset($_GET['count_only'])) {
			return $this['pager'];
		}
		return $this['users'] = $this['pager']->fetchPage();
	}

	/**
	 * Form to create or edit a User. Example:
	 * GET /users/edit/1
	 *
	 * @return User
	 */
	function edit($id = null) {
		return $this->getUser($id)->fromArray(@$_GET);
	}

	/**
	 * Saves a User. Examples:
	 * POST /users/save/1
	 * POST /rest/users/.json
	 * PUT /rest/users/1.json
	 */
	function save($id = null) {
		$user = $this->getUser($id);

		try {
			unset($_REQUEST['passwordHash']);
			$user->fromArray($_REQUEST);
			if (!empty($_REQUEST['password'])) {
				$hash = password_hash($_REQUEST['password'],  PASSWORD_DEFAULT);
				if ($hash !== false) {
					$user->setPasswordHash($hash);
				}
			}
			if ($user->validate()) {
				$user->save();
				$this->flash['messages'][] = 'User saved';
				$this->redirect('users/show/' . $user->getId());
			}
			$this->flash['errors'] = $user->getValidationErrors();
		} catch (Exception $e) {
			$this->flash['errors'][] = $e->getMessage();
		}

		$this->redirect('users/edit/' . $user->getId() . '?' . http_build_query($_REQUEST));
	}

	/**
	 * Returns the User with the id. Examples:
	 * GET /users/show/1
	 * GET /rest/users/1.json
	 *
	 * @return User
	 */
	function show($id = null) {
		return $this->getUser($id);
	}

	/**
	 * Deletes the User with the id. Examples:
	 * GET /users/delete/1
	 * DELETE /rest/users/1.json
	 */
	function delete($id = null) {
		$user = $this->getUser($id);

		try {
			if (null !== $user && $user->delete()) {
				$this['messages'][] = 'User deleted';
			} else {
				$this['errors'][] = 'User could not be deleted';
			}
		} catch (Exception $e) {
			$this['errors'][] = $e->getMessage();
		}

		if ($this->outputFormat === 'html') {
			$this->flash['errors'] = @$this['errors'];
			$this->flash['messages'] = @$this['messages'];
			$this->redirect('users');
		}
	}

	/**
	 * @return User
	 */
	private function getUser($id = null) {
		// look for id in param or in $_REQUEST array
		if (null === $id && isset($_REQUEST[User::getPrimaryKey()])) {
			$id = $_REQUEST[User::getPrimaryKey()];
		}

		if ('' === $id || null === $id) {
			// if no primary key provided, create new User
			$this['user'] = new User;
		} else {
			// if primary key provided, retrieve the record from the db
			$this['user'] = User::retrieveByPK($id);
		}
		return $this['user'];
	}

}