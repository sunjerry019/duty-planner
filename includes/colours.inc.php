<?php
	function getColorFromText($str)
	{
		$farbe = stringToColorCode($str);
		if (idealTextColour($farbe) == "#000000")
		{
			if(idealTextColour(stringToColorCode(substr($farbe, 1, 3))) == "#000000") $farbe = RGBToHex(getOpposite($farbe));
			
			while(idealTextColour($farbe) == "#000000") $farbe = changeColorLuminance($farbe, -0.05);
		}
		return $farbe;
	}

	// https://stackoverflow.com/a/3724294
	function stringToColorCode($str) 
	{
		$code = dechex(crc32($str));
		$code = substr($code, 0, 6);
		return "#".$code;
	}
	
	// https://stackoverflow.com/a/4726403
	function idealTextColour($bg) 
	{
		$nThreshold = 105;
		$components = HexToRGB($bg);
		$bgDelta = ($components["r"] * 0.299) + ($components["g"] * 0.587) + ($components["b"] * 0.114);
		
		return ((255 - $bgDelta) < $nThreshold) ? "#000000" : "#ffffff";
	}

	function HexToRGB($color)
	{
		$r = substr($color, 1, 2);
		$g = substr($color, 3, 2);
		$b = substr($color, 5, 2);
		
		return array(
			"r" => intval($r, 16),
			"g" => intval($g, 16),
			"b" => intval($b, 16)
		);
	}
	
	function componentToHex($c)
	{
		$hex = dechex($c);
		return strlen($hex) == 1 ? "0".$hex : $hex;
	}

	function RGBToHex($rgb)
	{
		return "#" . componentToHex($rgb["r"]) . componentToHex($rgb["g"]) . componentToHex($rgb["b"]);
	}
	
	// https://stackoverflow.com/a/1664186
	function getOpposite($color)
	{
		$temprgb = HexToRGB($color);
		$temphsv = RGBToHSV($temprgb);
		$temphsv["h"] = HueShift($temphsv["h"], 180.0);
		$temprgb = HSVToRGB($temphsv);
		
		return $temprgb;
	}
	
	// https://stackoverflow.com/a/13887939
	function RGBToHSV($rgb)    // RGB values:    0-255, 0-255, 0-255
	{                          // HSV values:    0-360, 0-100, 0-100
		// Convert the RGB byte-values to percentages
		$R = ($rgb["r"] / 255);
		$G = ($rgb["g"] / 255);
		$B = ($rgb["b"] / 255);

		// Calculate a few basic values, the maximum value of R,G,B, the
		//   minimum value, and the difference of the two (chroma).
		$maxRGB = max($R, $G, $B);
		$minRGB = min($R, $G, $B);
		$chroma = $maxRGB - $minRGB;

		// Value (also called Brightness) is the easiest component to calculate,
		//   and is simply the highest value among the R,G,B components.
		// We multiply by 100 to turn the decimal into a readable percent value.
		$computedV = 100 * $maxRGB;

		// Special case if hueless (equal parts RGB make black, white, or grays)
		// Note that Hue is technically undefined when chroma is zero, as
		//   attempting to calculate it would cause division by zero (see
		//   below), so most applications simply substitute a Hue of zero.
		// Saturation will always be zero in this case, see below for details.
		if ($chroma == 0)
			return array(0, 0, $computedV);

		// Saturation is also simple to compute, and is simply the chroma
		//   over the Value (or Brightness)
		// Again, multiplied by 100 to get a percentage.
		$computedS = 100 * ($chroma / $maxRGB);

		// Calculate Hue component
		// Hue is calculated on the "chromacity plane", which is represented
		//   as a 2D hexagon, divided into six 60-degree sectors. We calculate
		//   the bisecting angle as a value 0 <= x < 6, that represents which
		//   portion of which sector the line falls on.
		if ($R == $minRGB)
			$h = 3 - (($G - $B) / $chroma);
		elseif ($B == $minRGB)
			$h = 1 - (($R - $G) / $chroma);
		else // $G == $minRGB
			$h = 5 - (($B - $R) / $chroma);

		// After we have the sector position, we multiply it by the size of
		//   each sector's arc (60 degrees) to obtain the angle in degrees.
		$computedH = 60 * $h;

		return array("h" => round($computedH), "s" => round($computedS),  "v" => round($computedV));
	}

	function HSVToRGB($hsv)
	{
		if ($hsv["s"] == 0) $r = $g = $b = round($hsv["v"] * 2.55);
		else
		{
			$hsv["h"] /= 60;
			$hsv["s"] /= 100;
			$hsv["v"] /= 100;
			$i = floor($hsv["h"]);
			$f = $hsv["h"] - $i;
			$p = $hsv["v"] * (1 - $hsv["s"]);
			$q = $hsv["v"] * (1 - $hsv["s"] * $f);
			$t = $hsv["v"] * (1 - $hsv["s"] * (1 - $f));
			switch($i)
			{
				case 0:
					$r = $hsv["v"];
					$g = $t;
					$b = $p;
					break;
				case 1:
					$r = $q;
					$g = $hsv["v"];
					$b = $p;
					break;
				case 2:
					$r = $p;
					$g = $hsv["v"];
					$b = $t;
					break;
				case 3:
					$r = $p;
					$g = $q;
					$b = $hsv["v"];
					break;
				case 4:
					$r = $t;
					$g = $p;
					$b = $hsv["v"];
					break;
				default:
					$r = $hsv["v"];
					$g = $p;
					$b = $q;
			}
			$r = round($r * 255);
			$g = round($g * 255);
			$b = round($b * 255);
		}
		return array("r" => $r, "g" => $g, "b" => $b);
	}

	function HueShift($h, $s)
	{
		$h += $s;
		$h %= 360.0;
		if ($h < 0.0) $h += 360.0;
		return $h;
	}
	
	// http://www.sitepoint.com/javascript-generate-lighter-darker-color/
	function changeColorLuminance($hex, $lum)
	{
		// validate hex string
		preg_replace('/[^0-9a-f]/gi', '', $hex);
		if(strlen($hex) < 6) $hex = $hex[0] + $hex[0] + $hex[1] + $hex[1] + $hex[2] + $hex[2];
		$dlum = $lum ?? 0;
		
		// convert to decimal and change luminosity
		$rgb = "#";
		for($i = 0; $i < 3; $i++)
		{
			$c = intval(substr($hex, $i*2, 2), 16);
			$c = dechex(round(min(max(0, $c + ($c * $dlum)), 255)));
			$rgb .= substr("00".$c, strlen($c));
		}

		return $rgb;
	}
?>