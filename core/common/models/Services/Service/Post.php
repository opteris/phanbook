<?php
/**
 * Phanbook : Delightfully simple forum software
 *
 * Licensed under The GNU License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link    http://phanbook.com Phanbook Project
 * @since   1.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 */
namespace Phanbook\Models\Services\Service;

use Phanbook\Models\Karma;
use Phanbook\Models\Users;
use Phanbook\Models\Posts;
use Phanbook\Models\PostsViews;
use Phalcon\Mvc\Model\Exception;
use Phanbook\Models\Services\Service;

/**
 * \Phanbook\Models\Services\Service\Post
 *
 * @package Phanbook\Models\Services\Service
 */
class Post extends Service
{
    /**
     * Finds Post by ID.
     *
     * @param  int $id The Posts ID.
     * @return Posts|null
     */
    public function findFirstById($id)
    {
        return Posts::findFirstById($id) ?: null;
    }

    /**
     * Get Post by ID.
     *
     * @param  int $id The Posts ID.
     * @return Posts
     *
     * @throws Exception
     */
    public function getFirstById($id)
    {
        if (!$post = $this->findFirstById($id)) {
            throw new Exception(
                sprintf('No Posts found for ID %d', $id)
            );
        }

        return $post;
    }

    /**
     * Checks whether the Post is published.
     *
     * @param  Posts $post
     * @return bool
     */
    public function isPublished(Posts $post)
    {
        return $post->getStatus() == Posts::PUBLISH_STATUS && !$post->getDeleted();
    }

    /**
     * Checks whether the Post has views by ip address.
     *
     * @param Posts  $post
     * @param string $ipAddress
     *
     * @return int
     */
    public function hasViewsByIpAddress(Posts $post, $ipAddress = null)
    {
        if (!$ipAddress && $this->getDI()->has('request')) {
            $ipAddress = $this->getDI()->getShared('request')->getClientAddress();
        }

        return $this->countViewsByIpAddress($post, $this->resolveClientAddress($ipAddress)) > 0;
    }

    /**
     * Count views by ip address.
     *
     * @param Posts  $post
     * @param string $ipAddress
     *
     * @return int
     */
    public function countViewsByIpAddress($post, $ipAddress)
    {
        if (!$ipAddress) {
            return 0;
        }

        return $post->getPostview(['ipaddress = ?0', 'bind' => [$ipAddress]])->count();
    }

    /**
     * Increase number of views.
     *
     * @param  Posts  $post
     * @param  int    $visitorId
     * @param  string $ipAddress
     * @return $this
     */
    public function increaseNumberViews($post, $visitorId = null, $ipAddress = null)
    {
        $visitorId = $this->resolveVisitorId($visitorId);

        if (!$visitorId || $this->hasViewsByIpAddress($post, $visitorId)) {
            return $this;
        }

        $view = new PostsViews([
            'postsId'   => $post->getId(),
            'ipaddress' => $this->resolveClientAddress($ipAddress),
        ]);

        if (!$view->save()) {
            foreach ($post->getMessages() as $message) {
                $this->logError($message);
            }
        }

        $post->setNumberViews($post->getNumberViews() + 1);

        $this->increaseAuthorKarmaByVisit($post, $visitorId);

        if ($post->save()) {
            foreach ($post->getMessages() as $message) {
                $this->logError($message);
            }
        }

        return $this;
    }

    /**
     * Increase author karma.
     *
     * @param  Posts  $post
     * @param  int    $visitorId
     * @return $this
     */
    public function increaseAuthorKarmaByVisit($post, $visitorId = null)
    {
        if ($this->isAuthorVisitor($post, $visitorId)) {
            return $this;
        }

        if ($post->user->getStatus() != Users::STATUS_ACTIVE) {
            return $this;
        }

        $post->user->increaseKarma(Karma::VISIT_ON_MY_POST);

        return $this;
    }

    /**
     * Checks whether the current visitor is the author of the Post.
     *
     * @param Posts $post
     * @param int   $visitorId
     *
     * @return bool
     */
    public function isAuthorVisitor($post, $visitorId = null)
    {
        $visitorId = $this->resolveVisitorId($visitorId);

        return $visitorId && $post->getUsersId() == $visitorId;
    }
}
