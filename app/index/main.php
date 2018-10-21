<?php
/**
 * Created by PhpStorm.
 * User: 11641
 * Date: 2018/10/18
 * Time: 13:59
 */

if (!defined('IN_ANWSION'))
{
    die;
}

class main extends AWS_CONTROLLER
{

    public function get_access_rule()
    {
        $rule_action['rule_type'] = 'white';
        $rule_action['actions'][] = 'index';
        return $rule_action;
    }

    public function index_action()
    {

        if ($_GET['category'])
        {
            if (is_digits($_GET['category']))
            {
                $category_info = $this->model('system')->get_category_info($_GET['category']);
            }
            else
            {
                $category_info = $this->model('system')->get_category_info_by_url_token($_GET['category']);
            }
        }

        if ($category_info)
        {
            TPL::assign('category_info', $category_info);

            $this->crumb($category_info['title'], '/category-' . $category_info['id']);

            $meta_description = $category_info['title'];

            if ($category_info['description'])
            {
                $meta_description .= ' - ' . $category_info['description'];
            }

            TPL::set_meta('description', $meta_description);
        }
        if (! $_GET['sort_type'] AND !$_GET['is_recommend'])
        {
            $_GET['sort_type'] = 'new';
        }

        if ($_GET['sort_type'] == 'hot')
        {
            $posts_list = $this->model('posts')->get_hot_posts(null, $category_info['id'], null, $_GET['day'], $_GET['page'], get_setting('contents_per_page'));
        }
        else
        {
            $posts_list = $this->model('posts')->get_posts_list(null, $_GET['page'], get_setting('contents_per_page'), $_GET['sort_type'], null, $category_info['id'], $_GET['answer_count'], $_GET['day'], $_GET['is_recommend']);
        }

        if ($posts_list)
        {
            foreach ($posts_list AS $key => $val)
            {
                if ($val['answer_count'])
                {
                    $posts_list[$key]['answer_users'] = $this->model('question')->get_answer_users_by_question_id($val['question_id'], 2, $val['published_uid']);
                }
            }
        }

        TPL::assign('pagination', AWS_APP::pagination()->initialize(array(
            'base_url' => get_js_url('/sort_type-' . preg_replace("/[\(\)\.;']/", '', $_GET['sort_type']) . '__category-' . $category_info['id'] . '__day-' . intval($_GET['day']) . '__is_recommend-' . intval($_GET['is_recommend'])),
            'total_rows' => $this->model('posts')->get_posts_list_total(),
            'per_page' => get_setting('contents_per_page')
        ))->create_links());

        TPL::output('index/index');
    }
}