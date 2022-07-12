<?php

class PLong
{
  private $m_lValue = 0;
  
  public function getValue()
  {
    return $this->m_lValue;
  }
  
  public function setValue($l)
  {
    $this->m_lValue = $l;
  }
}

class Crc32
{
    private $Crc32Table;
    private $m_szCrc32;

    function __construct()
    {
        $this->Crc32Table[0] = 0;
        $this->Crc32Table[1] = 1996959894;
        $this->Crc32Table[2] = -301047508;
        $this->Crc32Table[3] = -1727442502;
        $this->Crc32Table[4] = 124634137;
        $this->Crc32Table[5] = 1886057615;
        $this->Crc32Table[6] = -379345611;
        $this->Crc32Table[7] = -1637575261;
        $this->Crc32Table[8] = 249268274;
        $this->Crc32Table[9] = 2044508324;
        $this->Crc32Table[10] = -522852066;
        $this->Crc32Table[11] = -1747789432;
        $this->Crc32Table[12] = 162941995;
        $this->Crc32Table[13] = 2125561021;
        $this->Crc32Table[14] = -407360249;
        $this->Crc32Table[15] = -1866523247;
        $this->Crc32Table[16] = 498536548;
        $this->Crc32Table[17] = 1789927666;
        $this->Crc32Table[18] = -205950648;
        $this->Crc32Table[19] = -2067906082;
        $this->Crc32Table[20] = 450548861;
        $this->Crc32Table[21] = 1843258603;
        $this->Crc32Table[22] = -187386543;
        $this->Crc32Table[23] = -2083289657;
        $this->Crc32Table[24] = 325883990;
        $this->Crc32Table[25] = 1684777152;
        $this->Crc32Table[26] = -43845254;
        $this->Crc32Table[27] = -1973040660;
        $this->Crc32Table[28] = 335633487;
        $this->Crc32Table[29] = 1661365465;
        $this->Crc32Table[30] = -99664541;
        $this->Crc32Table[31] = -1928851979;
        $this->Crc32Table[32] = 997073096;
        $this->Crc32Table[33] = 1281953886;
        $this->Crc32Table[34] = -715111964;
        $this->Crc32Table[35] = -1570279054;
        $this->Crc32Table[36] = 1006888145;
        $this->Crc32Table[37] = 1258607687;
        $this->Crc32Table[38] = -770865667;
        $this->Crc32Table[39] = -1526024853;
        $this->Crc32Table[40] = 901097722;
        $this->Crc32Table[41] = 1119000684;
        $this->Crc32Table[42] = -608450090;
        $this->Crc32Table[43] = -1396901568;
        $this->Crc32Table[44] = 853044451;
        $this->Crc32Table[45] = 1172266101;
        $this->Crc32Table[46] = -589951537;
        $this->Crc32Table[47] = -1412350631;
        $this->Crc32Table[48] = 651767980;
        $this->Crc32Table[49] = 1373503546;
        $this->Crc32Table[50] = -925412992;
        $this->Crc32Table[51] = -1076862698;
        $this->Crc32Table[52] = 565507253;
        $this->Crc32Table[53] = 1454621731;
        $this->Crc32Table[54] = -809855591;
        $this->Crc32Table[55] = -1195530993;
        $this->Crc32Table[56] = 671266974;
        $this->Crc32Table[57] = 1594198024;
        $this->Crc32Table[58] = -972236366;
        $this->Crc32Table[59] = -1324619484;
        $this->Crc32Table[60] = 795835527;
        $this->Crc32Table[61] = 1483230225;
        $this->Crc32Table[62] = -1050600021;
        $this->Crc32Table[63] = -1234817731;
        $this->Crc32Table[64] = 1994146192;
        $this->Crc32Table[65] = 31158534;
        $this->Crc32Table[66] = -1731059524;
        $this->Crc32Table[67] = -271249366;
        $this->Crc32Table[68] = 1907459465;
        $this->Crc32Table[69] = 112637215;
        $this->Crc32Table[70] = -1614814043;
        $this->Crc32Table[71] = -390540237;
        $this->Crc32Table[72] = 2013776290;
        $this->Crc32Table[73] = 251722036;
        $this->Crc32Table[74] = -1777751922;
        $this->Crc32Table[75] = -519137256;
        $this->Crc32Table[76] = 2137656763;
        $this->Crc32Table[77] = 141376813;
        $this->Crc32Table[78] = -1855689577;
        $this->Crc32Table[79] = -429695999;
        $this->Crc32Table[80] = 1802195444;
        $this->Crc32Table[81] = 476864866;
        $this->Crc32Table[82] = -2056965928;
        $this->Crc32Table[83] = -228458418;
        $this->Crc32Table[84] = 1812370925;
        $this->Crc32Table[85] = 453092731;
        $this->Crc32Table[86] = -2113342271;
        $this->Crc32Table[87] = -183516073;
        $this->Crc32Table[88] = 1706088902;
        $this->Crc32Table[89] = 314042704;
        $this->Crc32Table[90] = -1950435094;
        $this->Crc32Table[91] = -54949764;
        $this->Crc32Table[92] = 1658658271;
        $this->Crc32Table[93] = 366619977;
        $this->Crc32Table[94] = -1932296973;
        $this->Crc32Table[95] = -69972891;
        $this->Crc32Table[96] = 1303535960;
        $this->Crc32Table[97] = 984961486;
        $this->Crc32Table[98] = -1547960204;
        $this->Crc32Table[99] = -725929758;
        $this->Crc32Table[100] = 1256170817;
        $this->Crc32Table[101] = 1037604311;
        $this->Crc32Table[102] = -1529756563;
        $this->Crc32Table[103] = -740887301;
        $this->Crc32Table[104] = 1131014506;
        $this->Crc32Table[105] = 879679996;
        $this->Crc32Table[106] = -1385723834;
        $this->Crc32Table[107] = -631195440;
        $this->Crc32Table[108] = 1141124467;
        $this->Crc32Table[109] = 855842277;
        $this->Crc32Table[110] = -1442165665;
        $this->Crc32Table[111] = -586318647;
        $this->Crc32Table[112] = 1342533948;
        $this->Crc32Table[113] = 654459306;
        $this->Crc32Table[114] = -1106571248;
        $this->Crc32Table[115] = -921952122;
        $this->Crc32Table[116] = 1466479909;
        $this->Crc32Table[117] = 544179635;
        $this->Crc32Table[118] = -1184443383;
        $this->Crc32Table[119] = -832445281;
        $this->Crc32Table[120] = 1591671054;
        $this->Crc32Table[121] = 702138776;
        $this->Crc32Table[122] = -1328506846;
        $this->Crc32Table[123] = -942167884;
        $this->Crc32Table[124] = 1504918807;
        $this->Crc32Table[125] = 783551873;
        $this->Crc32Table[126] = -1212326853;
        $this->Crc32Table[127] = -1061524307;
        $this->Crc32Table[128] = -306674912;
        $this->Crc32Table[129] = -1698712650;
        $this->Crc32Table[130] = 62317068;
        $this->Crc32Table[131] = 1957810842;
        $this->Crc32Table[132] = -355121351;
        $this->Crc32Table[133] = -1647151185;
        $this->Crc32Table[134] = 81470997;
        $this->Crc32Table[135] = 1943803523;
        $this->Crc32Table[136] = -480048366;
        $this->Crc32Table[137] = -1805370492;
        $this->Crc32Table[138] = 225274430;
        $this->Crc32Table[139] = 2053790376;
        $this->Crc32Table[140] = -468791541;
        $this->Crc32Table[141] = -1828061283;
        $this->Crc32Table[142] = 167816743;
        $this->Crc32Table[143] = 2097651377;
        $this->Crc32Table[144] = -267414716;
        $this->Crc32Table[145] = -2029476910;
        $this->Crc32Table[146] = 503444072;
        $this->Crc32Table[147] = 1762050814;
        $this->Crc32Table[148] = -144550051;
        $this->Crc32Table[149] = -2140837941;
        $this->Crc32Table[150] = 426522225;
        $this->Crc32Table[151] = 1852507879;
        $this->Crc32Table[152] = -19653770;
        $this->Crc32Table[153] = -1982649376;
        $this->Crc32Table[154] = 282753626;
        $this->Crc32Table[155] = 1742555852;
        $this->Crc32Table[156] = -105259153;
        $this->Crc32Table[157] = -1900089351;
        $this->Crc32Table[158] = 397917763;
        $this->Crc32Table[159] = 1622183637;
        $this->Crc32Table[160] = -690576408;
        $this->Crc32Table[161] = -1580100738;
        $this->Crc32Table[162] = 953729732;
        $this->Crc32Table[163] = 1340076626;
        $this->Crc32Table[164] = -776247311;
        $this->Crc32Table[165] = -1497606297;
        $this->Crc32Table[166] = 1068828381;
        $this->Crc32Table[167] = 1219638859;
        $this->Crc32Table[168] = -670225446;
        $this->Crc32Table[169] = -1358292148;
        $this->Crc32Table[170] = 906185462;
        $this->Crc32Table[171] = 1090812512;
        $this->Crc32Table[172] = -547295293;
        $this->Crc32Table[173] = -1469587627;
        $this->Crc32Table[174] = 829329135;
        $this->Crc32Table[175] = 1181335161;
        $this->Crc32Table[176] = -882789492;
        $this->Crc32Table[177] = -1134132454;
        $this->Crc32Table[178] = 628085408;
        $this->Crc32Table[179] = 1382605366;
        $this->Crc32Table[180] = -871598187;
        $this->Crc32Table[181] = -1156888829;
        $this->Crc32Table[182] = 570562233;
        $this->Crc32Table[183] = 1426400815;
        $this->Crc32Table[184] = -977650754;
        $this->Crc32Table[185] = -1296233688;
        $this->Crc32Table[186] = 733239954;
        $this->Crc32Table[187] = 1555261956;
        $this->Crc32Table[188] = -1026031705;
        $this->Crc32Table[189] = -1244606671;
        $this->Crc32Table[190] = 752459403;
        $this->Crc32Table[191] = 1541320221;
        $this->Crc32Table[192] = -1687895376;
        $this->Crc32Table[193] = -328994266;
        $this->Crc32Table[194] = 1969922972;
        $this->Crc32Table[195] = 40735498;
        $this->Crc32Table[196] = -1677130071;
        $this->Crc32Table[197] = -351390145;
        $this->Crc32Table[198] = 1913087877;
        $this->Crc32Table[199] = 83908371;
        $this->Crc32Table[200] = -1782625662;
        $this->Crc32Table[201] = -491226604;
        $this->Crc32Table[202] = 2075208622;
        $this->Crc32Table[203] = 213261112;
        $this->Crc32Table[204] = -1831694693;
        $this->Crc32Table[205] = -438977011;
        $this->Crc32Table[206] = 2094854071;
        $this->Crc32Table[207] = 198958881;
        $this->Crc32Table[208] = -2032938284;
        $this->Crc32Table[209] = -237706686;
        $this->Crc32Table[210] = 1759359992;
        $this->Crc32Table[211] = 534414190;
        $this->Crc32Table[212] = -2118248755;
        $this->Crc32Table[213] = -155638181;
        $this->Crc32Table[214] = 1873836001;
        $this->Crc32Table[215] = 414664567;
        $this->Crc32Table[216] = -2012718362;
        $this->Crc32Table[217] = -15766928;
        $this->Crc32Table[218] = 1711684554;
        $this->Crc32Table[219] = 285281116;
        $this->Crc32Table[220] = -1889165569;
        $this->Crc32Table[221] = -127750551;
        $this->Crc32Table[222] = 1634467795;
        $this->Crc32Table[223] = 376229701;
        $this->Crc32Table[224] = -1609899400;
        $this->Crc32Table[225] = -686959890;
        $this->Crc32Table[226] = 1308918612;
        $this->Crc32Table[227] = 956543938;
        $this->Crc32Table[228] = -1486412191;
        $this->Crc32Table[229] = -799009033;
        $this->Crc32Table[230] = 1231636301;
        $this->Crc32Table[231] = 1047427035;
        $this->Crc32Table[232] = -1362007478;
        $this->Crc32Table[233] = -640263460;
        $this->Crc32Table[234] = 1088359270;
        $this->Crc32Table[235] = 936918000;
        $this->Crc32Table[236] = -1447252397;
        $this->Crc32Table[237] = -558129467;
        $this->Crc32Table[238] = 1202900863;
        $this->Crc32Table[239] = 817233897;
        $this->Crc32Table[240] = -1111625188;
        $this->Crc32Table[241] = -893730166;
        $this->Crc32Table[242] = 1404277552;
        $this->Crc32Table[243] = 615818150;
        $this->Crc32Table[244] = -1160759803;
        $this->Crc32Table[245] = -841546093;
        $this->Crc32Table[246] = 1423857449;
        $this->Crc32Table[247] = 601450431;
        $this->Crc32Table[248] = -1285129682;
        $this->Crc32Table[249] = -1000256840;
        $this->Crc32Table[250] = 1567103746;
        $this->Crc32Table[251] = 711928724;
        $this->Crc32Table[252] = -1274298825;
        $this->Crc32Table[253] = -1022587231;
        $this->Crc32Table[254] = 1510334235;
        $this->Crc32Table[255] = 755167117;
    }

    function getStrCrc32()
    {
        return $this->m_szCrc32;
    }

    function CalcCrc32($string, $i, PLong $plong)
    {
        $l = 0;

        if ($i <= 0)
        {
          $this->m_szCrc32 = "";
          return 0;
        }
        
        $l_1_;
        if ($plong->getValue() == 0)
          $l_1_ = 4294967295;
        else
          $l_1_ = $plong->getValue() ^ -1; //0xffffffffffffffff

        if (strlen($string) > 0 && $i > 0)
        {
          for ($i_2_ = 0; $i_2_ < $i; $i_2_++)
          {
            $c = substr($string, $i_2_, 1);
            $l_3_ = ($l_1_ ^ ord($c)) & 255; //0xff
            $l_4_ = $this->Crc32Table[$l_3_];
            $l_1_ = $l_1_ >> 8 & 16777215 ^ $l_4_; //0xffffff
          }
      
          if ($plong->getValue() != 0)
          {
            $plong->setValue($l_1_ ^ -1); //0xffffffffffffffff
            if ($plong->getValue() < 0)
              $plong->setValue(4294967296 + $plong->getValue());
          }
        }

        $l = $l_1_ ^ -1; //0xffffffffffffffff
    
        if ($l < 0)
          $l = 4294967296 + $l;

        $string_5_ = "0000000000";
        $string_6_ = "";
        $string_6_ = $l;
        $i_7_ = 10 - strlen($string_6_);

        if ($i_7_ == 0)
          $this->m_szCrc32 = $string_6_;
        else
          $this->m_szCrc32 = substr($string_5_, 0, $i_7_ - 1) + $string_6_; //a função substring do java exclui o último dígito da string

        return $l;
    }
}

?>