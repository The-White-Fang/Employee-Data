<?php

function decode_tech($arr) {
	$arr['tech'] = json_decode($arr['tech']);
	return $arr;
}

function dateMysqltoPHP ($date) {
	return implode('-', array_reverse(explode("-",$date)));
}

function adaptDate ($arr) {
	$arr['dob'] = dateMysqltoPHP($arr['dob']);
	return $arr;
}