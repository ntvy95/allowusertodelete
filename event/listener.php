<?php

namespace koutogima\allowusertodelete\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.posting_modify_template_vars' => 'posting_modify_template_vars',
			//'core.modify_posting_auth' => 'modify_posting_auth',
			'core.viewtopic_modify_post_action_conditions' => 'viewtopic_modify_post_action_conditions',
		);
	}
	protected $user;
	protected $auth;
	protected $config;
	protected $phpbb_content_visibility;
	
	public function __construct(\phpbb\user $user, \phpbb\auth\auth $auth,
	\phpbb\config\config $config, \phpbb\content_visibility $phpbb_content_visibility) {
		$this->user = $user;
		$this->auth = $auth;
		$this->config = $config;
		$this->phpbb_content_visibility = $phpbb_content_visibility;
	}
	
	public function posting_modify_template_vars($event) {
		$page_data = $event['page_data'];
		$page_data['S_DELETE_ALLOWED'] = ($event['mode'] == 'edit'
		&& (($event['post_data']['poster_id'] == $this->user->data['user_id']
		&& $this->auth->acl_get('f_delete', $event['forum_id'])
		&& !$event['post_data']['post_edit_locked']
		&& ($event['post_data']['post_time'] > time() - ($this->config['delete_time'] * 60)
		|| !$this->config['delete_time']))
		|| $this->auth->acl_get('m_delete', $event['forum_id'])))
		? true : false;
		$event['page_data'] = $page_data;
	}
	
	public function modify_posting_auth($event) {
		switch($event['mode']) {
			case 'delete': 
				if ($user->data['is_registered']
				&& ($this->auth->acl_get('m_delete', $event['forum_id'])
				|| ($event['post_data']['poster_id'] == $this->user->data['user_id']
				&& $this->auth->acl_get('f_delete', $event['forum_id']))))
				{
					$event['is_authed'] = true;
				}
			case 'soft_delete':
				if (!$event['is_authed']
				&& $this->user->data['is_registered']
				&& $this->phpbb_content_visibility->can_soft_delete($event['forum_id'], $event['post_data']['poster_id'], $event['post_data']['post_edit_locked']))
				{
					// Fall back to soft_delete if we have no permissions to delete posts but to soft delete them
					$event['is_authed'] = true;
					$event['mode'] = 'soft_delete';
				}
				else if (!$$event['is_authed'])
				{
					// Display the same error message for softdelete we use for delete
					$event['mode'] = 'delete';
				}
			break;
		}
	}
	
	public function viewtopic_modify_post_action_conditions($event) {
		$event['s_cannot_delete_lastpost'] = false;
	}
}