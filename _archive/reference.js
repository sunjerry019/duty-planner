//ensures that bg colour allows for white text overlay
function getColourfromText(str)
{
	var co = stringToColour(str);
	if(idealTextColor(co) == "#000000")
	{
		//console.log(str, co, idealTextColor(stringToColour(co.substr(1,3))));
		if(idealTextColor(stringToColour(co.substr(1, 3))) == "#000000") co = rgbToHex(getOpposite(co));

		while(idealTextColor(co) == "#000000")
		{
			co = changeColorLuminance(co, -0.05);
		}

	}

	return co;
}

function componentToHex(c)
{
	var hex = c.toString(16);
	return hex.length == 1 ? "0" + hex : hex;
}

function rgbToHex(rgb)
{
	return "#" + componentToHex(rgb.r) + componentToHex(rgb.g) + componentToHex(rgb.b);
}
//credit: https://stackoverflow.com/questions/5623838/rgb-to-hex-and-hex-to-rgb#562413

function stringToColour(str)
{

	// str to hash
	for(var i = 0, hash = 0;i < str.length;hash = str.charCodeAt(i++) + ((hash << 5) - hash)) { }

	// int/hash to hex
	for(var i = 0, colour = "#";i < 3;colour += ("00" + ((hash >> i++ * 8) & 0xFF).toString(16)).slice(-2)) { }

	return colour;
}
//credit: http://stackoverflow.com/questions/3426404/create-a-hexadecimal-colour-based-on-a-string-with-javascript#16348977


function idealTextColor(bgColor)
{

	var nThreshold = 105;
	var components = getRGBComponents(bgColor);
	var bgDelta = (components.r * 0.299) + (components.g * 0.587) + (components.b * 0.114);

	return ((255 - bgDelta) < nThreshold) ? "#000000" : "#ffffff";
}

function getRGBComponents(color)
{

	var r = color.substring(1, 3);
	var g = color.substring(3, 5);
	var b = color.substring(5, 7);

	return {
		r: parseInt(r, 16),
		g: parseInt(g, 16),
		b: parseInt(b, 16)
	};
}
//credit http://stackoverflow.com/questions/4726344/how-do-i-change-text-color-determined-by-the-background-color#answer-4726403

function getOpposite(color)
{
	var temprgb = getRGBComponents(color);
	var temphsv = RGB2HSV(temprgb);

	temphsv.hue = HueShift(temphsv.hue, 180.0);
	temprgb = HSV2RGB(temphsv);

	return temprgb;
}

function RGB2HSV(rgb)
{
	hsv = new Object();
	max = max3(rgb.r, rgb.g, rgb.b);
	dif = max - min3(rgb.r, rgb.g, rgb.b);
	hsv.saturation = (max == 0.0) ? 0 : (100 * dif / max);
	if(hsv.saturation == 0) hsv.hue = 0;
	else if(rgb.r == max) hsv.hue = 60.0 * (rgb.g - rgb.b) / dif;
	else if(rgb.g == max) hsv.hue = 120.0 + 60.0 * (rgb.b - rgb.r) / dif;
	else if(rgb.b == max) hsv.hue = 240.0 + 60.0 * (rgb.r - rgb.g) / dif;
	if(hsv.hue < 0.0) hsv.hue += 360.0;
	hsv.value = Math.round(max * 100 / 255);
	hsv.hue = Math.round(hsv.hue);
	hsv.saturation = Math.round(hsv.saturation);
	return hsv;
}

// RGB2HSV and HSV2RGB are based on Color Match Remix [http://color.twysted.net/]
// which is based on or copied from ColorMatch 5K [http://colormatch.dk/]
function HSV2RGB(hsv)
{
	var rgb = new Object();
	if(hsv.saturation == 0)
	{
		rgb.r = rgb.g = rgb.b = Math.round(hsv.value * 2.55);
	} else
	{
		hsv.hue /= 60;
		hsv.saturation /= 100;
		hsv.value /= 100;
		i = Math.floor(hsv.hue);
		f = hsv.hue - i;
		p = hsv.value * (1 - hsv.saturation);
		q = hsv.value * (1 - hsv.saturation * f);
		t = hsv.value * (1 - hsv.saturation * (1 - f));
		switch(i)
		{
			case 0:
				rgb.r = hsv.value;
				rgb.g = t;
				rgb.b = p;
				break;
			case 1:
				rgb.r = q;
				rgb.g = hsv.value;
				rgb.b = p;
				break;
			case 2:
				rgb.r = p;
				rgb.g = hsv.value;
				rgb.b = t;
				break;
			case 3:
				rgb.r = p;
				rgb.g = q;
				rgb.b = hsv.value;
				break;
			case 4:
				rgb.r = t;
				rgb.g = p;
				rgb.b = hsv.value;
				break;
			default:
				rgb.r = hsv.value;
				rgb.g = p;
				rgb.b = q;
		}
		rgb.r = Math.round(rgb.r * 255);
		rgb.g = Math.round(rgb.g * 255);
		rgb.b = Math.round(rgb.b * 255);
	}
	return rgb;
}

//Adding HueShift via Jacob (see comments)
function HueShift(h, s)
{
	h += s;
	while(h >= 360.0) h -= 360.0;
	while(h < 0.0) h += 360.0;
	return h;
}

//min max via Hairgami_Master (see comments)
function min3(a, b, c)
{
	return (a < b) ? ((a < c) ? a : c) : ((b < c) ? b : c);
}

function max3(a, b, c)
{
	return (a > b) ? ((a > c) ? a : c) : ((b > c) ? b : c);
}
//credit https://stackoverflow.com/questions/1664140/js-function-to-calculate-complementary-colour#1664186

function changeColorLuminance(hex, lum)
{

	// validate hex string
	hex = String(hex).replace(/[^0-9a-f]/gi, '');
	if(hex.length < 6)
	{
		hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
	}
	lum = lum || 0;

	// convert to decimal and change luminosity
	var rgb = "#",
		c, i;
	for(i = 0;i < 3;i++)
	{
		c = parseInt(hex.substr(i * 2, 2), 16);
		c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16);
		rgb += ("00" + c).substr(c.length);
	}

	return rgb;
}
//credit http://www.sitepoint.com/javascript-generate-lighter-darker-color/