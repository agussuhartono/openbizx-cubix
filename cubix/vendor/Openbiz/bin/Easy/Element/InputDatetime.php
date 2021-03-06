<?PHP
/**
 * Openbiz Framework
 *
 * LICENSE
 *
 * This source file is subject to the BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @package   openbiz.bin.easy.element
 * @copyright Copyright (c) 2005-2011, Rocky Swen
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @link      http://www.phpopenbiz.org/
 * @version   $Id: InputDatetime.php 2553 2010-11-21 08:36:48Z mr_a_ton $
 */

namespace Openbiz\Easy\Element;

use Openbiz\Openbiz;
use Openbiz\Easy\Element\InputText;

/**
 * InputDatetime class is element for input date and time with datetime picker
 *
 * @package openbiz.bin.easy.element
 * @author Rocky Swen
 * @copyright Copyright (c) 2005-2009
 * @access public
 */
class InputDatetime extends InputText {
    public $dateFormat;

    /**
     * Read array meta data, and store to meta object
     *
     * @param array $xmlArr
     * @return void
     */
    protected function readMetaData(&$xmlArr) {
        parent::readMetaData($xmlArr);
        $this->dateFormat  = isset($xmlArr["ATTRIBUTES"]["DATEFORMAT"]) ? $xmlArr["ATTRIBUTES"]["DATEFORMAT"] : null;
    }

    /**
     * Render / draw the element according to the mode
     *
     * @return string HTML text
     */
    public function render() {
        Openbiz::$app->getClientProxy()->includeCalendarScripts();

        $format = $this->dateFormat ? $this->dateFormat : "%Y-%m-%d %H:%M:%S";

        $sHTML = parent::render();

        $showTime = "'24'";
        //$image = "<img src=\"".Openbiz::$app->getImageUrl()."/calendar.gif\" border=0 title=\"Select date...\" align='top' hspace='2'>";
        $sHTML .= "<a title=\"Select date...\"  class=\"date_picker\" href=\"javascript: void(0);\" onclick=\"return showCalendar('".$this->objectName."', '".$format."', ".$showTime.", true); return false;\"  onmousemove='window.status=\"Select a datetime\"' onmouseout='window.status=\"\"'></a>";
        return $sHTML;
    }
}
