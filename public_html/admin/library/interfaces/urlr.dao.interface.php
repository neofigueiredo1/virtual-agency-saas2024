<?php
interface UrlRewriteDAO_interface{
	public function insert(UrlRewriteRule $Rule);
	public function update(UrlRewriteRule $Rule);
	public function delete(UrlRewriteRule $Rule);
	public function toString(UrlRewriteRule $Rule);
	public function get($id);
	public function getAll();
}