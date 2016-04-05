<?php

class {{ controller_name }} extends {{ base_controller_name }}
{
	/**
	 * @access Role
	 */
	public function indexAction()
	{
		$condition = Strings::filter($_GET);
		$result = {{model_name}}::search($condition);

		$count = $result['count'];
		$items = $result['items'];
		$data = array(
			'count' => $count,
			'items' => $items, 
			'i' => $condition,
			'item_has_checkbox' => {{ item_has_checkbox }},
			'item_has_operator' => {{ item_has_operator }},
			'headers' => {{model_name}}::headers(),
			'target_field' => {{model_name}}::primaryKeyName()
			);
		$views = array('name' => '查看列表', 'template' => '{{lower(model_name)}}/index');
		parent::showPager($_GET['__pager_current'], $count);
		parent::showTabViews($views, '查看', $data);
	}

	/**
	 * @access Role
	 */
	public function createAction()
	{
		$views = [
			['name' => '新建', "template" => "{{lower(model_name)}}/create"],
		];
		$init = {{model_name}}::getEmptyItem();
		$data = array(
			'itemViewMode' => 'create', 
			'formSaveActionUrl' => '{{firstlower(model_name)}}/save',
			// TODO: Commit support
			'i' => $init);
		parent::showTabViews($views, '新建', $data);
	}

	/**
	 * @param $id
	 * @access Role
	 */
	public function updateAction($id)
	{
		if (!isset($id)) {
			parent::redirect('common/error', "This action need parameter \$id");
		}
		$views = [
			['name' => '编辑', "template" => "{{lower(model_name)}}/update"],
		];
		$item = {{model_name}}::getItemById($id);
		$data = array(
			'itemViewMode' => 'update', 
			'formSaveActionUrl' => "{{firstlower(model_name)}}/save/$id",
			'i' => $item);
		parent::showTabViews($views, '编辑', $data);

	}

	public function viewAction($id) {
		if (!isset($id)) {
			parent::redirect('common/error', "This action need parameter \$id");
		}
		$views = [
			['name' => '查看', "template" => "{{lower(model_name)}}/detail"],
		];
		$item = {{model_name}}::getItemById($id);
		$data = array('itemViewMode' => 'view', 'i' => $item);
		parent::showTabViews($views, '查看', $data);
	}

	/**
	 * @param $id int
	 * @access Role
	 */
	public function saveAction($id = 0)
	{
		$post = $this->request->getPost();

		if (!$id) {
			$obj = new {{model_name}}();
			{% if create_time %}
			$post['{{create_time}}'] = date('Y-m-d H:i:s');{% end %}
			{% if update_time %}
			$post['{{update_time}}'] = date('Y-m-d H:i:s');{% end %}
		} else {
			$obj = {{model_name}}::findFirst($id);
            {% if update_time %}
			$post['{{update_time}}'] = date('Y-m-d H:i:s');{% end %}
		}

		if ($obj->save($post)) {
			// TODO: If save success, go to index, otherwise show Error
			return parent::result($post);
		} else {
            $reason = array();
            foreach ($obj->getMessages() as $message) {
                array_push($reason, $message->getMessage());
            }
            return parent::error(1, array('post' => $post, 'reason' => $reason));
        }
	}	

	/**
	 * @param $id
	 * @access Role
	 */
	public function deleteAction($id)
	{
		$item = {{model_name}}::findFirst($id);
		$fieldInfo = {{model_name}}::fieldForDelete();
		$fieldDelete = $fieldInfo['field'];
		$valueDelete = $fieldInfo['value'];
		if ($fieldDelete)
		{
			$item->$fieldDelete = $valueDelete;
		
			$deleted = $item->save();
			return parent::result(array('id' => $id, 'deleted' => $deleted));
		}
		return parent::error(-1, array('error' => 'No field set for deletion'));
	}

	public function itemOperator() {
		// array for operators
		return array(
			array('name' => '编辑', 'operator' => 'edit', 'action' => '{{ firstlower(model_name) }}/update'),
			{% if support_delete == 'Yes' %}array('name' => '删除', 'operator' => 'delete', 'action' => '{{ firstlower(model_name) }}/delete'){% end %}
		);
	}
}