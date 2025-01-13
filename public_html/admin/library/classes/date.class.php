<?php
/*
* Date
* Classe com métodos auxiliares relcionados à data.
*/
class Date{

	public static function getWeekDay($date = "") {
		$weekDay = array(1 => "Segunda",2 => "Terça",3 => "Quarta",4 => "Quinta",5 => "Sexta",6 => "Sábado",0 => "Domingo");
		$date = date_format(date_create($date), 'w');
		return $weekDay[$date];
	}

	public static function toMysql($date, $type = 1)
	{
		$type = (int)$type;

		$newDate = $date;
		$dateArray = explode("/",$date);
		if(count($dateArray)==3){$newDate = $dateArray[1]."/".$dateArray[0]."/".$dateArray[2];}
		$date = $newDate;

		if(count($dateArray)==3)
		{
			if (checkdate($dateArray[1], $dateArray[0], substr($dateArray[2],0,4))) {
				if($type===1){ $date = date_format(date_create($date), 'Y-m-d'); }
				if($type===2){ $date = date_format(date_create($date), 'Y-m-d H:i:s'); }
			}else{
				if($type===1){ $date = '0000-00-00'; }
				if($type===2){ $date = '0000-00-00 00:00:00'; }
			}
		}else{
			if($type===1){ $date = '0000-00-00'; }
			if($type===2){ $date = '0000-00-00 00:00:00'; }
		}

		return $date;
	}

	public static function fromMysql($date,$type = 1){
		$type = (int)$type;

		if($type===1){ $date = date_format(date_create($date), 'd/m/Y'); }
		if($type===2){ $date = date_format(date_create($date), 'H:i:s d/m/Y'); }
		if($type===3){ $date = date_format(date_create($date), 'd/m/Y H:i:s'); }

		return $date;
	}

	public static function isDate($string)
	{
		if($string!=""){
			$dateArray = explode("/",$string);
			if(count($dateArray)!=3)
			{
				return false;
			}else{
				$stamp = strtotime($string);
				$month = date( 'm', $stamp );
				$day   = date( 'd', $stamp );
				$year  = date( 'Y', $stamp );
				return checkdate( $month, $day, $year );
			}
		}
		else{
			return false;
		}
	}

	public static function getMonth($value=''){
		$month = "";
		switch ($value) {
			case "01":    $month = "Janeiro";     break;
			case "02":    $month = "Fevereiro";   break;
			case "03":    $month = "Março";       break;
			case "04":    $month = "Abril";       break;
			case "05":    $month = "Maio";        break;
			case "06":    $month = "Junho";       break;
			case "07":    $month = "Julho";       break;
			case "08":    $month = "Agosto";      break;
			case "09":    $month = "Setembro";    break;
			case "10":    $month = "Outubro";     break;
			case "11":    $month = "Novembro";    break;
			case "12":    $month = "Dezembro";    break;
		}
		return $month;
	}

	public static function calculateTimePast($dateOrigin){
		$seconds = strtotime(date("r",strtotime($dateOrigin))) - strtotime(date("r"));
		$minutes = "";
		$hours = "";
		$days = "";
		if ($seconds<0) $seconds=abs($seconds);
		$d = round($seconds/86400,0);

		if ($d > 0) {
			if ($d == 1) {
				$days = $d . " dia ";
			}else{
				$days = $d . " dias ";
			}
			$seconds = $seconds % 86400;
		}
		$h = round($seconds/3600,0);
		if ($h > 0) {
			$hours = $h . " horas ";
			$seconds = $seconds % 3600;
		}
		$mm = round($seconds/60,0);
		if ($mm > 0) {
			$minutes = $mm . " min ";
			$seconds = $seconds % 60;
		}
		$tDiff = $days . $hours . $minutes;
		if ($days == "") {
			$tDiff = $tDiff . $seconds . " seg";
		}
		return $tDiff;
	}

	public static function getMicrotimeFloat()
	{
	    list($usec, $sec) = explode(" ", microtime());
	    return ((float)$usec + (float)$sec);
	}

}