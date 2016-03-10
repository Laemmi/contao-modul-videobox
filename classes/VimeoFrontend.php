<?php
/**
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @category    contao-module-videobox
 * @package     Vimeo
 * @author      Michael Lämmlein <laemmi@spacerabbit.de>
 * @copyright   ©2016 laemmi
 * @license     http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version     1.0.0
 * @since       10.03.16
 */

/**
 * Namespace
 */
namespace Laemmi\Videobox;

/**
 * Class VimeoFrontend
 */
class VimeoFrontend extends \Frontend
{
	/**
	 * Youtube URL
	 * @var string
	 */
	public $strVimeoUrl = '';	
	
	
	/**
	 * Youtube URL
	 * @var string
	 */
	public $strTemplate = '';
	
	/**
	 * Data array
	 * @var array
	 */
	public $arrData = array();
	
	/**
	 * Parse the array data and prepare for the Youtube video
	 * @param array
	 * @return array
	 */
	public function parseVideo($arrDBData)
	{
		$this->import('String');
		
		// set template
		$this->strTemplate = (strlen($arrDBData['vimeo_template'])) ? $arrDBData['vimeo_template'] : 'videobox_vimeo';
		
		$this->arrData['id'] = 'video_' . md5(uniqid(mt_rand(), true));
		$this->arrData['timestamp'] = $arrDBData['tstamp'];
		$this->arrData['video_title'] = $arrDBData['videotitle'];
		$this->arrData['archive_title'] = $arrDBData['title'];
		
		// size
		if(!strlen($arrDBData['vimeo_size'])) {
			$arrSize = array(425,344);
		} else {
			$arrSize = deserialize($arrDBData['vimeo_size']);
		}
		
		$this->arrData['width'] = $arrSize[0];
		$this->arrData['height'] = $arrSize[1];
		$this->arrData['autoplay'] = (strlen($arrDBData['vimeo_autoplay']) && TL_MODE == 'FE') ? true : false;
		$this->arrData['color'] = (strlen($arrDBData['vimeo_color'])) ? $arrDBData['vimeo_color'] : 'F7FFFD';
        $this->arrData['showbyline'] = (strlen($arrDBData['vimeo_showbyline'])) ? true : false;
        $this->arrData['showtitle'] = (strlen($arrDBData['vimeo_showtitle'])) ? true : false;
        $this->arrData['showportrait'] = (strlen($arrDBData['vimeo_showportrait'])) ? true : false;

		$this->arrData['params'] = (strlen($arrDBData['vimeo_autoplay']) && TL_MODE == 'FE') ? 'autoplay=1' : 'autoplay=0';
		$this->arrData['params'].= (strlen($arrDBData['vimeo_color'])) ? '&color=' . $arrDBData['vimeo_color'] : 'ffffff';
		$this->arrData['params'].= (strlen($arrDBData['vimeo_showbyline'])) ? '&byline=1' : '&byline=0';
		$this->arrData['params'].= (strlen($arrDBData['vimeo_showtitle'])) ? '&title=1' : '&title=0';
		$this->arrData['params'].= (strlen($arrDBData['vimeo_showportrait'])) ? '&portrait=1' : '&portrait=0';

		$this->strVimeoUrl = 'http://www.vimeo.com/moogaloop.swf?clip_id=' . $arrDBData['vimeo_id'] . '&server=www.vimeo.com&';
		$this->strVimeoUrl.= $this->arrData['params'];
		
		$this->arrData['vimeolink'] = $this->strVimeoUrl;
		$this->arrData['vimeoid'] = $arrDBData['vimeo_id'];

		// usability, useless as vimeo supports html5
		$this->arrData['noscript'] = $this->String->decodeEntities(sprintf($GLOBALS['TL_LANG']['VideoBox']['vimeo_noscript'], $arrDBData['videotitle']));
		$this->arrData['noflash'] = $this->String->decodeEntities(sprintf($GLOBALS['TL_LANG']['VideoBox']['vimeo_noflash'], $arrDBData['videotitle']));

		// Template
		$objTemplate = new \FrontendTemplate($this->strTemplate);
		$objTemplate->setData($this->arrData);
		return $objTemplate->parse();
	}
}