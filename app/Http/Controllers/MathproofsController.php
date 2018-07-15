<?php namespace App\Http\Controllers;

use App\Services\Mathproof;
use App\Services\Existingword;
use App\Services\ExistingwordMathproof;
use App\Services\Comment;
use App\Services\Accurateflag;
use App\Http\Requests\postRequest;
use App\Helpers\Helper;
use Input;
use DB;
use DOMDocument;
use Redirect;
use Validator;
use Auth;

class MathproofsController extends Controller {
	
	public function index($page = 1) {
		
		return $this->search(null, $page);
		
	}
	
	public function form_search() {
		
		if(Input::has('home_search')) {
			$search = Input::get('home_search');
		}
		elseif(Input::has('re_search')) {
			$search = Input::get('re_search');
		}
		else {
			return redirect('/mathproofs');
		}
		
		$clean_search = preg_replace("/[^a-zA-Z0-9]+/", " ", $search);
		return redirect("/mathproofs/search/{$clean_search}/1");
	
	}
	
	public function search($search = null, $page = 1) {
		
		//Sanitize page input
		$page = preg_replace("/[^0-9]+/", "", $page);

		//Set pagination values
		$per_page = 10;
		$max_results = 100;
		
		//Get matches
		if(isset($search)) {
			$search = preg_replace("/[^a-zA-Z0-9]+/", " ", $search);
			$helper = new Helper;
			$search_words = $helper->string_to_clean_array($search);
			$total_matches = 
				DB::table('existingwords')
				->join('existingword_mathproofs', 'existingwords.id', '=', 'existingword_mathproofs.existingword_id')
				->whereIn('existingwords.word', $search_words)
				->select(DB::raw('count(distinct existingword_mathproofs.mathproof_id) as total_proofs'))
				->first();
			$total_results = min($total_matches->total_proofs, $max_results);
			$total_pages = ceil(1.0 * $total_results / $per_page);
			$matches = 
				DB::table('existingwords')
				->join('existingword_mathproofs', 'existingwords.id', '=', 'existingword_mathproofs.existingword_id')
				->whereIn('existingwords.word', $search_words)
				->select(DB::raw('existingword_mathproofs.mathproof_id, count(1) as matching_words'))
				->groupBy('existingword_mathproofs.mathproof_id')
				->orderBy('matching_words', 'desc')
				->orderBy('mathproof_id', 'desc')
				->limit($max_results)
				->skip($per_page * ($page - 1))
				->take($per_page)
				->get();	
			if($total_results > ($page * $per_page)) {
				$next_page = 1;
			}
			else {
				$next_page = 0;
			}
			if($page > 1) {
				$previous_page = 1;
			}
			else {
				$previous_page = 0;
			}
			$recent = 0;
			$page_base_url  = '/mathproofs/search/' . $search . '/';
		}
		else{
			$total_results = min(Mathproof::count(), $max_results);
			$total_pages = ceil(1.0 * $total_results / $per_page);
			$matches = 
				Mathproof::orderBy('created_at', 'desc')
				->limit($max_results)
				->skip($per_page * ($page - 1))
				->take($per_page)
				->select(DB::raw('id as mathproof_id'))
				->get();
			if($total_results > ($page * $per_page)) {
				$next_page = 1;
			}
			else {
				$next_page = 0;
			}
			if($page > 1) {
				$previous_page = 1;
			}
			else {
				$previous_page = 0;
			}
			$recent = 1;
			$page_base_url  = '/mathproofs/recent/';
		}
		
		//Return the appropriate results
		$mathproofs = array();
		if(count($matches) > 0) {
			foreach($matches as $match) {
							$mathproof = Mathproof::where('id', '=', $match->mathproof_id)
								->first(['slug_id', 'theorem_words', 'theorem_symbolic', 'branches', 'flagged_accurate', 'created_at', 'username']);
							if(count($mathproof) > 0) {
								array_push($mathproofs, $mathproof);
							}
			}
		}
		
		return view('mathproofs/search', compact('search', 'mathproofs', 'page', 'total_pages', 'next_page', 'previous_page', 'page_base_url', 'recent'));
	}
	
	public function create() {
		
		return view('mathproofs/addproof');
		
	}
	
	protected function storeRules() {
		
		return [
			'theorem_words' => 'required',
			'branches' => 'required',
			'proof' => 'required'
		];
		
	}
	
	protected function storeMessages() {
		
	    return [
	        'theorem_words.required' => 'Please enter the theorem in words, and be as verbose as possible!',
	        'branches.required' => 'Please enter the field(s) of mathematics. It makes the proof more easily searchable.',
	        'proof.required' => 'The proof is required.'
	    ];
	    
	}
	
	public function store(postRequest $request) {
		
		//First validate the request
		$validator = Validator::make($request->all(), $this->storeRules(), $this->storeMessages());
		if ($validator->fails()) {
			return Redirect::to('/mathproofs/create')->withErrors($validator->errors())->with('request', $request->all());
		}
		
		//Sanitize proof
		$helper = new Helper;
		$proof = $request->proof;
		$proof_safe = str_ireplace('<section ', '<div ', $proof);
		$proof_safe = str_ireplace('</section>', '</div>', $proof_safe);
		$allowed_tags_proof = array(
			'p', 'strong', 'u', 'em', 'sub', 'sup', 'ol', 'li', 'ul', 'blockquote', 'img', 
			'span', 'a', 'div', 'cite', 'pre', 'code', 'table', 'caption', 'tbody', 'tr', 'td'
		);
		$proof_safe = strip_tags($proof_safe, '<' . implode('><', $allowed_tags_proof) . '>');
		$dom_proof = new DOMDocument();
		$dom_proof->loadHTML($proof_safe);
		foreach($allowed_tags_proof as $allowed_tag_proof) {
			$proof_safe = $helper->clean_html_by_tag($dom_proof, $proof_safe, $allowed_tag_proof);
		}
		
		//Sanitize theorem symbolic
		$theorem_symbolic_safe = $request->theorem_symbolic;
		if($theorem_symbolic_safe != '') {
			$theorem_symbolic_safe = strip_tags($theorem_symbolic_safe, '<p><span>');
			$dom_thm_smblc = new DOMDocument();
			$dom_thm_smblc->loadHTML($theorem_symbolic_safe);
			$theorem_symbolic_safe = $helper->clean_html_by_tag($dom_thm_smblc, $theorem_symbolic_safe, 'p');
			$theorem_symbolic_safe = $helper->clean_html_by_tag($dom_thm_smblc, $theorem_symbolic_safe, 'span');
			$theorem_symbolic_safe = str_ireplace('<p>&nbsp;</p>', '', $theorem_symbolic_safe);
			$theorem_symbolic_safe = str_ireplace('<p></p>', '', $theorem_symbolic_safe);
			$theorem_symbolic_safe = str_ireplace('</p>', '<br>', $theorem_symbolic_safe);
			$theorem_symbolic_safe = str_ireplace('<p>', '', $theorem_symbolic_safe);
			$theorem_symbolic_safe = trim($theorem_symbolic_safe);
		}
		
		//Generate unique slug id for proof based on theorem words
		$slug_id_init = str_slug($request->theorem_words, '_');
		$unique_slug = 0;
		$slug_count = 1;
		$slug_id = $slug_id_init;
		while ($unique_slug == 0) {
				$existing_proof = Mathproof::where('slug_id', '=', $slug_id)->first(['id', 'slug_id']);
				if(count($existing_proof) > 0) {
					$slug_count++;
					$slug_id = $slug_id_init . $slug_count;
				}
				else {
					$unique_slug = 1;
				}
		}
		
		//Save the new proof
		$newproof_data = array(
			"slug_id" => $slug_id,
			"user_id" => Auth::user()->id,
			"username" => Auth::user()->username,
			"theorem_words" => $request->theorem_words,
			"theorem_symbolic" => $theorem_symbolic_safe,
			"branches" => $request->branches,
			"proof" => $proof_safe
		);
		$new_mathproof = Mathproof::create($newproof_data);
		
		//Make the proof searchable by adding to the existing words and existing word mathproofs tables
		$helper = new Helper;
		$theorem_words = $helper->string_to_clean_array($request->theorem_words);
		$branch_words = $helper->string_to_clean_array($request->branches);
		foreach($branch_words as $branch_word) {
			if(!in_array($branch_word, $theorem_words)) {
				array_push($theorem_words, $branch_word);
			}
			else {}
		}
		foreach($theorem_words as $theorem_word) {
			$existing_thm_word = Existingword::where('word', '=', $theorem_word)->first(['id']);
			$new_wordproof = new ExistingwordMathproof;
			$new_wordproof->mathproof_id = $new_mathproof->id;
			if(count($existing_thm_word) > 0) {
				$new_wordproof->existingword_id = $existing_thm_word->id;
			}
			else {
				$new_existingword = new Existingword;
				$new_existingword->word = $theorem_word;
				$new_existingword->save();
				$new_wordproof->existingword_id = $new_existingword->id;
			}
			$new_wordproof->save();
		}
		
		return Redirect::to('/mathproofs/' . $slug_id)->with('good_message', 'Congratulations! Your math proof is posted!');
		
	}
	
	public function show($slug_id) {
		
		$mathproof = Mathproof::where('slug_id', '=', $slug_id)->firstOrFail([
			'theorem_words',
			'theorem_symbolic',
			'branches',
			'username',
			'created_at',
			'flagged_accurate',
			'proof',
			'slug_id',
			'id'
		]);
		$comments = Comment::where('mathproof_id', '=', $mathproof->id)->orderBy('created_at', 'desc')->get([
			'username',
			'created_at',
			'comment'
		]);
		$already_flagged = 0;
		if(Auth::user()) {
			$flag = Accurateflag::where('mathproof_id', '=', $mathproof->id)->where('user_id', '=', Auth::user()->id)->first();
			if(count($flag) > 0) {
				$already_flagged = 1;
			}
		}
		return view('mathproofs/showproof', compact('mathproof', 'comments', 'already_flagged'));
		
	}
	
}
