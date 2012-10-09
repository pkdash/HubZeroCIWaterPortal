<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

/**
 * Short description for 'DocumentMetadata'
 * 
 * Long description (if any) ...
 */
abstract class DocumentMetadata
{

	/**
	 * Description for 'stop_words'
	 * 
	 * @var array
	 */
	private static $stop_words = array(
		'1' => 1, '2' => 1, '3' => 1, '4' => 1, '5' => 1, '6' => 1, '7' => 1, '8' => 1, '9' => 1, '0' => 1, 'a' => 1, 'about' => 1, 'above' => 1, 'across' => 1, 'after' => 1, 'again' => 1, 'against' => 1, 'all' => 1,
		'almost' => 1, 'alone' => 1, 'along' => 1, 'already' => 1, 'also' => 1, 'although' => 1, 'always' => 1, 'among' => 1, 'an' => 1, 'and' => 1, 'another' => 1, 'any' => 1, 'anybody' => 1, 'anyone' => 1,
		'anything' => 1, 'anywhere' => 1, 'are' => 1, 'area' => 1, 'areas' => 1, 'around' => 1, 'as' => 1, 'ask' => 1, 'asked' => 1, 'asking' => 1, 'asks' => 1, 'at' => 1, 'away' => 1, 'b' => 1, 'back' => 1, 'backed' => 1,
		'backing' => 1, 'backs' => 1, 'be' => 1, 'became' => 1, 'because' => 1, 'become' => 1, 'becomes' => 1, 'been' => 1, 'before' => 1, 'began' => 1, 'behind' => 1, 'being' => 1, 'beings' => 1, 'best' => 1, 'better' => 1,
		'between' => 1, 'big' => 1, 'both' => 1, 'but' => 1, 'by' => 1, 'c' => 1, 'came' => 1, 'can' => 1, 'cannot' => 1, 'case' => 1, 'cases' => 1, 'certain' => 1, 'certainly' => 1, 'clear' => 1, 'clearly' => 1, 'come' => 1,
		'could' => 1, 'd' => 1, 'did' => 1, 'differ' => 1, 'different' => 1, 'differently' => 1, 'do' => 1, 'does' => 1, 'done' => 1, 'down' => 1, 'downed' => 1, 'downing' => 1, 'downs' => 1, 'during' => 1, 'e' => 1,
		'each' => 1, 'early' => 1, 'either' => 1, 'end' => 1, 'ended' => 1, 'ending' => 1, 'ends' => 1, 'enough' => 1, 'even' => 1, 'evenly' => 1, 'ever' => 1, 'every' => 1, 'everybody' => 1, 'everyone' => 1,
		'everything' => 1, 'everywhere' => 1, 'f' => 1, 'face' => 1, 'faces' => 1, 'far' => 1, 'felt' => 1, 'few' => 1, 'find' => 1, 'finds' => 1, 'first' => 1, 'for' => 1, 'four' => 1, 'from' => 1,
		'full' => 1, 'fully' => 1, 'further' => 1, 'furthered' => 1, 'furthering' => 1, 'furthers' => 1, 'g' => 1, 'gave' => 1, 'general' => 1, 'generally' => 1, 'get' => 1, 'gets' => 1, 'give' => 1, 'given' => 1, 'gives' => 1,
		'go' => 1, 'going' => 1, 'good' => 1, 'goods' => 1, 'got' => 1, 'great' => 1, 'greater' => 1, 'greatest' => 1, 'group' => 1, 'grouped' => 1, 'grouping' => 1, 'groups' => 1, 'h' => 1, 'had' => 1, 'has' => 1, 'have' => 1,
		'having' => 1, 'he' => 1, 'her' => 1, 'here' => 1, 'herself' => 1, 'high' => 1, 'higher' => 1, 'highest' => 1, 'him' => 1, 'himself' => 1, 'his' => 1, 'how' => 1, 'however' => 1, 'i' => 1, 'if' => 1, 'important' => 1,
		'in' => 1, 'interest' => 1, 'interested' => 1, 'interesting' => 1, 'interests' => 1, 'into' => 1, 'is' => 1, 'it' => 1, 'its' => 1, 'itself' => 1, 'j' => 1, 'just' => 1, 'k' => 1, 'keep' => 1, 'keeps' => 1, 'kind' => 1,
		'knew' => 1, 'know' => 1, 'known' => 1, 'knows' => 1, 'l' => 1, 'large' => 1, 'largely' => 1, 'last' => 1, 'later' => 1, 'latest' => 1, 'least' => 1, 'less' => 1, 'let' => 1, 'lets' => 1, 'like' => 1, 'likely' => 1,
		'long' => 1, 'longer' => 1, 'longest' => 1, 'm' => 1, 'made' => 1, 'make' => 1, 'making' => 1, 'man' => 1, 'many' => 1, 'may' => 1, 'me' => 1, 'member' => 1, 'members' => 1, 'men' => 1, 'might' => 1, 'more' => 1,
		'most' => 1, 'mostly' => 1, 'mr' => 1, 'mrs' => 1, 'much' => 1, 'must' => 1, 'my' => 1, 'myself' => 1, 'n' => 1, 'necessary' => 1, 'need' => 1, 'needed' => 1, 'needing' => 1, 'needs' => 1, 'never' => 1, 'new' => 1,
		'newer' => 1, 'newest' => 1, 'next' => 1, 'no' => 1, 'nobody' => 1, 'non' => 1, 'noone' => 1, 'not' => 1, 'nothing' => 1, 'now' => 1, 'nowhere' => 1, 'number' => 1, 'numbers' => 1, 'o' => 1, 'of' => 1, 'off' => 1,
		'often' => 1, 'old' => 1, 'older' => 1, 'oldest' => 1, 'on' => 1, 'once' => 1, 'one' => 1, 'only' => 1, 'open' => 1, 'opened' => 1, 'opening' => 1, 'opens' => 1, 'or' => 1, 'order' => 1, 'ordered' => 1, 'ordering' => 1,
		'orders' => 1, 'other' => 1, 'others' => 1, 'our' => 1, 'out' => 1, 'over' => 1, 'p' => 1, 'part' => 1, 'parted' => 1, 'parting' => 1, 'parts' => 1, 'per' => 1, 'perhaps' => 1, 'place' => 1, 'places' => 1, 'point' => 1,
		'pointed' => 1, 'pointing' => 1, 'possible' => 1, 'present' => 1, 'presented' => 1, 'presenting' => 1, 'presents' => 1, 'problem' => 1, 'problems' => 1, 'put' => 1, 'puts' => 1, 'q' => 1, 'quite' => 1,
		'r' => 1, 'rather' => 1, 'really' => 1, 'right' => 1, 'right' => 1, 'room' => 1, 'rooms' => 1, 's' => 1, 'said' => 1, 'same' => 1, 'saw' => 1, 'say' => 1, 'says' => 1, 'second' => 1, 'seconds' => 1, 'see' => 1,
		'seem' => 1, 'seemed' => 1, 'seeming' => 1, 'seems' => 1, 'sees' => 1, 'several' => 1, 'shall' => 1, 'she' => 1, 'should' => 1, 'show' => 1, 'showed' => 1, 'showing' => 1, 'shows' => 1, 'side' => 1, 'sides' => 1,
		'since' => 1, 'small' => 1, 'smaller' => 1, 'smallest' => 1, 'so' => 1, 'some' => 1, 'somebody' => 1, 'someone' => 1, 'something' => 1, 'somewhere' => 1, 'states' => 1, 'still' => 1, 'such' => 1,
		'sure' => 1, 't' => 1, 'take' => 1, 'taken' => 1, 'than' => 1, 'that' => 1, 'the' => 1, 'their' => 1, 'them' => 1, 'then' => 1, 'there' => 1, 'therefore' => 1, 'these' => 1, 'they' => 1, 'thing' => 1, 'things' => 1,
		'think' => 1, 'thinks' => 1, 'this' => 1, 'those' => 1, 'though' => 1, 'thought' => 1, 'thoughts' => 1, 'three' => 1, 'through' => 1, 'thus' => 1, 'to' => 1, 'today' => 1, 'together' => 1, 'too' => 1, 'took' => 1,
		'toward' => 1, 'turn' => 1, 'turned' => 1, 'turning' => 1, 'turns' => 1, 'two' => 1, 'u' => 1, 'under' => 1, 'unless' => 1, 'until' => 1, 'up' => 1, 'upon' => 1, 'us' => 1, 'use' => 1, 'used' => 1, 'uses' => 1,
		'v' => 1, 'very' => 1, 'w' => 1, 'want' => 1, 'wanted' => 1, 'wanting' => 1, 'wants' => 1, 'was' => 1, 'way' => 1, 'ways' => 1, 'we' => 1, 'well' => 1, 'wells' => 1, 'went' => 1, 'were' => 1, 'what' => 1, 'when' => 1,
		'where' => 1, 'whether' => 1, 'which' => 1, 'while' => 1, 'who' => 1, 'whole' => 1, 'whose' => 1, 'why' => 1, 'will' => 1, 'with' => 1, 'within' => 1, 'without' => 1, 'work' => 1, 'worked' => 1, 'working' => 1,
		'works' => 1, 'would' => 1, 'x' => 1,  'y' => 1, 'year' => 1, 'years' => 1, 'yet' => 1, 'you' => 1, 'young' => 1, 'younger' => 1, 'youngest' => 1, 'your' => 1, 'yours' => 1, 'z' => 1
	);

	/**
	 * Short description for 'is_stop_word'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $word Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public static function is_stop_word($word)
	{
		return array_key_exists($word, self::$stop_words);
	}
}

/**
 * Short description for 'class'
 * 
 * Long description (if any) ...
 */
class YSearchModelTerms extends JModel
{

	/**
	 * Description for 'raw'
	 * 
	 * @var unknown
	 */
	private $raw, $positive_chunks, $optional_chunks = array(), $forbidden_chunks = array(), $mandatory_chunks = array(), $section = NULL, $quoted = array();

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $raw Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct($raw)
	{
		$this->raw = preg_replace('/^\s+|\s+$/', '', preg_replace('/\s+/', ' ', $raw));
		if ($this->is_set())
			$this->parse_searchable_chunks();
	}

	/**
	 * Short description for 'is_quoted'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $idx Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function is_quoted($idx)
	{
		return isset($this->quoted[$idx]) ? $this->quoted[$idx] : false;
	}

	/**
	 * Short description for 'get_raw'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function get_raw() { return $this->raw; }

	/**
	 * Short description for 'get_raw_without_section'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     string Return description (if any) ...
	 */
	public function get_raw_without_section()
	{
		if (!$this->section) return $this->raw;
		return preg_replace('/^'.implode(':', $this->section).':/', '', $this->raw);
	}

	/**
	 * Short description for 'get_section'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function get_section() { return $this->section; }

	/**
	 * Short description for 'get_optional_chunks'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function get_optional_chunks() { return $this->optional_chunks; }

	/**
	 * Short description for 'get_forbidden_chunks'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function get_forbidden_chunks() { return $this->forbidden_chunks; }

	/**
	 * Short description for 'get_mandatory_chunks'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function get_mandatory_chunks() { return $this->mandatory_chunks; }

	/**
	 * Short description for 'get_positive_chunks'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function get_positive_chunks()
	{
		if (!$this->positive_chunks)
			$this->positive_chunks = array_unique(array_merge($this->mandatory_chunks, $this->optional_chunks));
		return $this->positive_chunks;
	}

	/**
	 * Short description for 'get_stemmed_chunks'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     mixed Return description (if any) ...
	 */
	public function get_stemmed_chunks()
	{
		$chunks = $this->get_positive_chunks();
		foreach ($chunks as $term)
		{
			while (($stemmed = stem($term)) != $term)
			{
				$chunks[] = $stemmed;
				$term = $stemmed;
			}
		}
		$chunks = array_unique(array_merge(array_map('stem', $chunks), $chunks));
		JFactory::getApplication()->triggerEvent('onYSearchExpandTerms', array(&$chunks));

		return array_unique($chunks);
	}

	/**
	 * Short description for 'is_set'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function is_set()
	{
		return !!$this->raw;
	}

	/**
	 * Short description for 'any'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function any()
	{
		return !!($this->optional_chunks || $this->mandatory_chunks);
	}

	/**
	 * Short description for 'parse_searchable_chunks'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	private function parse_searchable_chunks()
	{
		$accumulating_phrase = false;
		$partial = '';
		$sign = '';
		$raw = trim(strtolower($this->raw));
		if (preg_match('/^([_.a-z:]+):/', $raw, $match))
		{
			$this->section = explode(':', $match[1]);
			$raw = preg_replace('/^'.preg_quote($match[1]).':/', '', $raw);
		}
		else if (array_key_exists('section', $_GET))
			$this->section = array($_GET['section']);

		$raw = preg_replace('#[^-:/\\+"[:alnum:] ]#', '', preg_replace('/\s+/', ' ', trim($raw)));
		for ($idx = 0, $len = strlen($raw); $idx < $len; ++$idx)
		{
			$cur = $raw[$idx];
			if ($accumulating_phrase)
			{
				if ($cur == '"')
				{
					$accumulating_phrase = false;
					$this->add_chunk($partial, $sign, true);
				}
				else
					$partial .= $cur;
			}
			else if ($cur == '"')
			{
				if ($partial)
					$this->add_chunk($partial, $sign);
				$accumulating_phrase = true;
			}
			else if ($cur == ' ')
				$this->add_chunk($partial, $sign);
			else if ($cur == '+' && $partial == '')
				$sign = '+';
			else if ($cur == '-' && $partial == '')
				$sign = '-';
			else if ($cur != ' ')
				$partial .= $cur;
		}
		$this->add_chunk($partial, $sign);
	}

	/**
	 * Short description for 'add_chunk'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string &$partial Parameter description (if any) ...
	 * @param      string &$sign Parameter description (if any) ...
	 * @param      boolean $quoted Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	private function add_chunk(&$partial, &$sign = '', $quoted = false)
	{
		if (!$partial) return;
		if (DocumentMetadata::is_stop_word($partial))
		{
			$partial = '';
			$sign = '';
			return;
		}
		if ($sign == '-')
			$this->forbidden_chunks[] = $partial;
		else if ($sign == '+')
			$this->mandatory_chunks[] = $partial;
		else
		{
			$this->optional_chunks[] = $partial;
			$this->quoted[count($this->optional_chunks) - 1] = $quoted;
		}

		$partial = '';
		$sign = '';
	}

	/**
	 * Short description for 'get_word_regex'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     string Return description (if any) ...
	 */
	public function get_word_regex()
	{
		$chunks = $this->get_stemmed_chunks();
		usort($chunks, create_function('$a, $b', '$al = strlen($a); $bl = strlen($b); if ($al == $bl) return 0; return $al > $bl ? -1 : 1;'));
		return '('.join('|', array_map('preg_quote', $chunks)).'[[:alpha:]]*)';
	}

	/**
	 * Short description for '__toString'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function __toString()
	{
		return $this->raw;
	}
}

