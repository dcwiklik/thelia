<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*	email : info@thelia.net                                                      */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.     */
/*                                                                                   */
/*************************************************************************************/


namespace Thelia\Core\Template\Loop;



use Thelia\Log\Tlog;
use Thelia\Tpex\Element\Loop\BaseLoop;
use Thelia\Model\CategoryQuery;

class Category extends BaseLoop {

    public $id;
    public $parent;
    public $current;
    public $not_empty;
    public $visible;
    public $link;
    public $order;
    public $random;
    public $exclude;
    public $start;
    public $offset;

    public function defineArgs()
    {
        return array(
            "id" => "optional",
            "parent" => "optional",
            "current" => "optional",
            "not_empty" => 0,
            "visible" => 1,
            "link" => "optional",
            "order" => "optional",
            "random" => 0,
            "exclude" => "optional",
            "limit" => 10,
            "offset" => 0,
        );
    }


    public function exec($text)
    {
        $search = CategoryQuery::create();

        if (!is_null($this->id)) {
            $search->filterById($this->id);
        }

        if(!is_null($this->parent)) {
            $search->filterByParent($this->parent);
        }

        if($this->current == 1) {
            $search->filterById($this->request->get("category_id"));
        } else if ($this->current == 0) {
            $search->filterById($this->request->get("category_id"), \Criteria::NOT_IN);
        }

        if (!is_null($this->exclude)) {
            $search->filterById(explode(",", $this->exclude), \Criteria::NOT_IN);
        }

        if (!is_null($this->link)) {
            $search->filterByLink($this->link);
        }

        if($this->limit > -1) {
            $search->limit($this->limit);
        }
        $search->offset($this->offset);

        switch($this->order) {
            case "alpha":
                $search->addAscendingOrderByColumn(\Thelia\Model\CategoryI18nPeer::TITLE);
                break;
            case "alpha_reverse":
                $search->addDescendingOrderByColumn(\Thelia\Model\CategoryI18nPeer::TITLE);
                break;
            case "reverse":
                $search->orderByPosition(\Criteria::DESC);
                break;
            default:
                $search->orderByPosition();
                break;
        }

        if($this->random == 1) {
            $search->clearOrderByColumns();
            $search->addAscendingOrderByColumn('RAND()');
        }
        $search->joinWithI18n('en_US');

        $categories = $search->find();

        $res = "";

        foreach ($categories as $category) {
            $temp = str_replace("#TITLE", $category->getTitle(), $text);
            $temp = str_replace("#CHAPO", $category->getChapo(), $temp);
            $temp = str_replace("#ID", $category->getId(), $temp);
            $res .= $temp;
        }

        return $res;
    }

}