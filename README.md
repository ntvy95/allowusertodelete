Date of creation: Jul 01, 2016.

I once put all of my phpBB extensions into one GitHub respository when I was a GitHub newbie. I am re-committing them for separate repositories.

# What is this?

I develop this extension based on phpbb 3.1.9 version referencing [this post](https://www.phpbb.com/community/viewtopic.php?p=13248088&sid=a82db79f6f5332dcf3ceafd5005024e3#p13248088) by ronjan (that's why I listed him / her as the first author, I hope he / she does not mind about this). Please note that if you follow the guide by ronjan solely on phpbb 3.1.9, you may not get what you expect and that is also the reason why I develop this extension.

# Before activating the extension

Open the file: ./includes/functions_posting.php

Find (the below block is inside the function phpbb_handle_post_delete):

`if ($auth->acl_get("m_$perm_check", $forum_id) || ($post_data['poster_id'] == $user->data['user_id'] && $user->data['is_registered'] && $auth->acl_get("f_$perm_check", $forum_id) && $post_id == $post_data['topic_last_post_id'] && !$post_data['post_edit_locked'] && ($post_data['post_time'] > time() - ($config['delete_time'] * 60) || !$config['delete_time'])))`

Replace it with (by removing " && $post_id == $post_data['topic_last_post_id']"):

 `if ($auth->acl_get("m_$perm_check", $forum_id) || ($post_data['poster_id'] == $user->data['user_id'] && $user->data['is_registered'] && $auth->acl_get("f_$perm_check", $forum_id) && !$post_data['post_edit_locked'] && ($post_data['post_time'] > time() - ($config['delete_time'] * 60) || !$config['delete_time'])))`

