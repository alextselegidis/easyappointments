<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

/**
 * About controller.
 *
 * Handles about settings related operations.
 *
 * @package Controllers
 */
class About extends EA_Controller
{
    /**
     * Cache key of the fetched blog posts.
     */
    private const BLOG_POSTS_CACHE_KEY = 'about-blog-posts';

    /**
     * Refetch the blog posts after this many seconds.
     */
    private const BLOG_POSTS_CACHE_TTL = 3600;

    /**
     * Keep the fetched blog posts around for this long, so that they remain available as a fallback.
     */
    private const BLOG_POSTS_CACHE_LIFETIME = 604800;

    /**
     * About constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('appointments_model');
        $this->load->model('customers_model');
        $this->load->model('services_model');
        $this->load->model('providers_model');
        $this->load->model('roles_model');
        $this->load->model('settings_model');

        $this->load->library('accounts');
        $this->load->library('google_sync');
        $this->load->library('notifications');
        $this->load->library('synchronization');
        $this->load->library('timezones');
    }

    /**
     * Render the settings page.
     */
    public function index(): void
    {
        method('get');

        session(['dest_url' => site_url('about')]);

        $user_id = session('user_id');

        if (cannot('view', PRIV_USER_SETTINGS)) {
            if ($user_id) {
                abort(403, 'Forbidden');
            }

            redirect('login');

            return;
        }

        $role_slug = session('role_slug');

        // Fetch blog posts from RSS feed
        $blog_posts = $this->fetch_blog_posts();

        script_vars([
            'user_id' => $user_id,
            'role_slug' => $role_slug,
        ]);

        html_vars([
            'page_title' => lang('about'),
            'active_menu' => PRIV_SYSTEM_SETTINGS,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
            'privileges' => $this->roles_model->get_permissions_by_slug($role_slug),
            'blog_posts' => $blog_posts,
        ]);

        $this->load->view('pages/about');
    }

    /**
     * Fetch blog posts from the Easy!Appointments RSS feed.
     *
     * @return array
     */
    private function fetch_blog_posts(): array
    {
        $this->load->driver('cache', ['adapter' => 'file']);

        $cached = $this->cache->get(self::BLOG_POSTS_CACHE_KEY) ?: [];

        // Only hit the remote feed once per hour, and fall back to the last known posts when it is unreachable.
        if (!empty($cached['posts']) && $cached['fetch_timestamp'] > time() - self::BLOG_POSTS_CACHE_TTL) {
            return $cached['posts'];
        }

        $blog_posts = [];

        try {
            $rss_url = 'https://easyappointments.org/feed/';

            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'user_agent' => 'Easy!Appointments/' . config('version'),
                ],
                'ssl' => [
                    'verify_peer' => true,
                    'verify_peer_name' => true,
                ],
            ]);

            $rss_content = @file_get_contents($rss_url, false, $context);

            if ($rss_content === false) {
                log_message('error', 'Failed to fetch RSS feed from ' . $rss_url);
                return $this->reuse_cached_blog_posts($cached);
            }

            $xml = @simplexml_load_string($rss_content);

            if ($xml === false) {
                log_message('error', 'Failed to parse RSS feed XML');
                return $this->reuse_cached_blog_posts($cached);
            }

            $count = 0;

            foreach ($xml->channel->item as $item) {
                if ($count >= 10) {
                    break;
                }

                $blog_posts[] = [
                    'title' => (string) $item->title,
                    'link' => (string) $item->link,
                    'pub_date' => (string) $item->pubDate,
                ];

                $count++;
            }
        } catch (Throwable $e) {
            log_message('error', 'Error fetching blog posts: ' . $e->getMessage());

            return $this->reuse_cached_blog_posts($cached);
        }

        if ($blog_posts) {
            $this->save_cached_blog_posts($blog_posts);
        }

        return $blog_posts;
    }

    /**
     * Reuse the previously fetched blog posts after a failed fetch attempt.
     *
     * The posts are stored again so that the unreachable feed is only retried once per cache TTL, instead of on
     * every single page load.
     *
     * @param array $cached Previously cached blog posts entry.
     *
     * @return array
     */
    private function reuse_cached_blog_posts(array $cached): array
    {
        $blog_posts = $cached['posts'] ?? [];

        if ($blog_posts) {
            $this->save_cached_blog_posts($blog_posts);
        }

        return $blog_posts;
    }

    /**
     * Store the blog posts in the cache, along with the current fetch timestamp.
     *
     * @param array $blog_posts Blog posts to be cached.
     */
    private function save_cached_blog_posts(array $blog_posts): void
    {
        $this->cache->save(
            self::BLOG_POSTS_CACHE_KEY,
            ['fetch_timestamp' => time(), 'posts' => $blog_posts],
            self::BLOG_POSTS_CACHE_LIFETIME,
        );
    }
}
