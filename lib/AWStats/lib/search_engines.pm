# AWSTATS SEARCH ENGINES DATABASE
#------------------------------------------------------------------------------
# If you want to add a Search Engine to extend AWStats database detection capabilities,
# you must add an entry in SearchEnginesSearchIDOrder, SearchEnginesHashID and in
# SearchEnginesHashLib.
# An entry if known in SearchEnginesKnownUrl is also welcome.
#------------------------------------------------------------------------------
# $Revision: 1.50 $ - $Author: eldy $ - $Date: 2009/09/12 11:06:54 $
# Modified by ホビット 2009/10/14
# 2005-08-19 Sean Carlos http://www.antezeta.com/awstats.html
#            added minor italian search engines
#                  arianna http://arianna.libero.it/
#                  supereva http://search.supereva.com/
#                  kataweb http://kataweb.it/
#            corrected uk looksmart
#                  'askuk','ask=', 'bbc','q=', 'freeserve','q=', 'looksmart','key=',
#            to
#                  'askuk','ask=', 'bbc','q=', 'freeserve','q=', 'looksmartuk','key=',
#            corrected spelling
#                     internationnal -> international
#            added 'google\.'=>'mail\.google\.', to NotSearchEnginesKeys in order to
#            avoid counting gmail referrals as search engine traffic
# 2005-08-21 Sean Carlos http://www.antezeta.com/awstats.html
#            avoid counting babelfish.altavista referrals as search engine traffic
#            avoid counting translate.google referrals as search engine traffic
# 2005-11-20 Sean Carlos
# 	     added missing 'tiscali','key=', entry.  Check order
# 2005-11-22 Sean Carlos
# 	     added Google Base & Froogle.  Froogle not tested.
# 2006-04-18 Sean Carlos http://www.antezeta.com/awstats.html
# 	     added biglotron.com (France)
# 	     added blingo http://www.blingo.com/
# 	     added Clusty & Vivisimo
# 	     added eniro.no (Norway) [https://sourceforge.net/forum/message.php?msg_id=3134783]
# 	     added GPU p2p search http://search.centraldatabase.org/
# 	     added mail.tiscali to "not search engines list" [https://sourceforge.net/forum/message.php?msg_id=3166688]
# 	     added Ask group's "mysearch"
# 	     added sify.com (India)
# 	     added sogou.com (Cina) [https://sourceforge.net/forum/message.php?msg_id=3501603]
# 	     Ask changes:
# 	     - added Ask Japan (ask.jp)
# 	     - break out Ask new country level variants (DE, ES, FR, IT, NL)
# 	     - updated Ask name from Ask Jevees
# 	     - added Ask q= parameter - many recent searches probably not recognized; [https://sourceforge.net/forum/message.php?msg_id=3465444]
# 	     - updated Ask uk (new uk.ask.com added to older ask.co.uk)
# 	     updated voila kw|rdata parameter [https://sourceforge.net/forum/message.php?msg_id=3373912]
#	     for each new engine, added link to Search Engine.  This serves to document engine. Done for major & Italian engines as well. Requires patch
#		to AWStats to allow untranslated html.  Otherwise html will appear instead of link.
#	     reviewed mnoGoSearch (http://www.mnogosearch.org/); the search engined mentioned no longer
#		exists https://sourceforge.net/forum/message.php?msg_id=3025426
# 2006-05-13 Sean Carlos http://www.antezeta.com/awstats.html
#            added 10 Chello European broadband portals (Austria, Belgium, Czech Republic, France, Hungary, The Netherlands, Norway, Poland, Slovakia, Sweden)
#	     added Alice Internal Search (blends data with Google?) search.alice.it.master:10005
#            added detection of google cache views from IPs 66.249.93.104 72.14.203.104 72.14.207.104
#		To do: add more extensive IP list; keywords not yet detected.
#            added icerocket.com blog search http://www.icerocket.com/
#	     added live.com (msn) http://www.live.com/
# 	     added Meta motor kartoo.  Note: Kartoo does not provide search words in referrers, thus the engine will appear in the
#		search engine list but the actual search words are not available.
#	     added netluchs.de http://www.netluchs.de/
#	     added sphere.com blog search http://www.sphere.com/
#	     added wwweasel.de http://wwweasel.de
#	     added Yahoo Mindset! http://mindset.research.yahoo.com/
#            updated Mirago query parameter recognition (qry=); added breakout for each country (France, Germany, Spain, Italy, Norway, Sweden, Denmark, Netherlands, Belgium, Switzerland)
# 2006-05-13 Sean Carlos http://www.antezeta.com/awstats.html
#	     added Google cache IPs 64.233.183.104 & 66.102.7.104
# 2006-05-20 Sean Carlos http://www.antezeta.com/awstats.html
#		anzwers.com.au
#		schoenerbrausen.de http://www.schoenerbrausen.de/
#		added Google cache IP 216.239.59.104
#		answerbus http://www.answerbus.com/ (does not provide keywords)
# 2006-05-23 Sean Carlos http://www.antezeta.com/awstats.html
#		added Google cache IP 66.102.9.104, 64.233.161.104
# 2006-06-23 Sean Carlos http://www.antezeta.com/awstats.html
#	     	added Alice Search search.alice.it
#		added GoodSearch http://www.goodsearch.com/ (does not provide keywords) "a Yahoo-powered search engine that donates money to your favorite charity or school each time you search the web"
#		added googlee.com, variant of Google
#		added gotuneed http://www.gotuneed.com/ Italian search engine, in beta
#		added icq.com
#		added logic to parse Google Cache search keywords. Seems to work for alpha but not numeric cache IDs, i.e. search?q=cache:lWVLmnuGJswJ: is recognized but q=cache:Yv5qxeJNuhgJ: is not recognized. The URL triggering the keywords will also appear.  The URLs are probably too varied to parse out?
#		added Nusearch http://www.nusearch.com/
#		added Polymeta www.polymeta.hu (does not provide keywords)
#		added scroogle http://www.scroogle.org/ (does not always provide keywords)
#		added Tango http://tango.hu/search.php?st=0&q=jeles+napok
#		Changed Google Cache notation 64\.233\.(161|167|179|183|187)\.104 to 64\.233\.1[0-9]{2}\.104
#		 			      72\.14\.(203|205|207|209|221)\.104 to 72\.14\.2[0-9]{2}\.104
#					      216\.239\.(51|59)\.104 to 216\.239\.5[0-9]\.104
#					      66\.102\.(7|9)\.104 to 66\.102\.[1-9]\.104
# 2006-06-27 Sean Carlos http://www.antezeta.com/awstats.html
#		added Onet.pl http://szukaj.onet.pl/
#		corrected name "Wirtualna Polska" from "Szukaj" (search); added link http://szukaj.wp.pl/
# 2006-06-30 Sean Carlos http://www.antezeta.com/awstats.html
#	Additional Polish Search Engines:
#	added Dodaj.pl http://www.dodaj.pl/
#	added Gazeta.pl http://szukaj.gazeta.pl/
#	added Gery.pl http://szukaj.gery.pl/
#	added Hoga.pl http://www.hoga.pl/
#	added Interia.pl http://www.google.interia.pl/
#	added Katalog.Onet.pl http://katalog.onet.pl/
#	added NetSprint.pl http://www.netsprint.pl/
#	added o2.pl http://szukaj2.o2.pl/
#	added Polska http://szukaj.polska.pl/
#	added Szukacz http://www.szukacz.pl/
#	added Wow.pl http://szukaj.wow.pl/
#	added Sagool http://sagool.jp/

# 2006-08-25 Social Bookmarks
#	International
#	added del.icio.us/search - for now, just search referrer. To do: consider /tag/(tagname) referrer?
# 	added stumbleupon.com - No keywords supplied.
#	added swik.net
#       added digg. Keywords sometimes supplied.
#	Italy
# 	added segnalo.alice.it - No keywords supplied.
#	added ineffabile.it - No keywords supplied.

#       added filter for google groups.  Attempt to parse group name as keyword.

# 2006-09-14
#	added Eniro Sverige http://www.eniro.se/
#	added MyWebSearch http://search.mywebsearch.com/
#	added Teecno http://www.teecno.it/ Italian Open Source Search Engine

#package AWSSE;

# 2006-09-25 (Gabor Moizes)
# added 4-counter (Google alternative) http://4-counter.com/
# added Googlecom (Google alternative) http://googlecom.com/
# added Goggle (Google alternative) http://goggle.co.hu/
# added Comet toolbar http://as.starware.com
# added new IP for Yahoo: 216.109.125.130
# added Ledix http://ledix.net/
# added AT&T search (powered by Google) http://www.att.net/
# added Keresolap (Hungarian search engine) http://www.keresolap.hu/
# added Mozbot (French search engine) http://www.mozbot.fr/
# added Zoznam (Slovak search engine) http://www.zoznam.sk/
# added sapo.pt (Portuguese search engine) http://www.sapo.pt/
# added shaw.ca (powered by Google) http://start.shaw.ca/
# added Searchalot http://www.searchalot.com/
# added Copernic http://www.copernic.com/
# added 216.109.125.130 to Yahoo
# added 66.218.69.11 to Yahoo
# added Avantfind http://www.avantfind.com/
# added Steadysearch http://www.steadysearch.com/
# added Steadysearch http://www.steady-search.com/
# modified 216\.239\.5[0-9]\.104/search to 216\.239\.5[0-9]\.104


# SearchEnginesSearchIDOrder
# It contains all matching criteria to search for in log fields. This list is
# used to know in which order to search Search Engines IDs.
# Most frequent one are in list1, used when LevelForSearchEnginesDetection is 1 or more
# Minor robots are in list2, used when LevelForSearchEnginesDetection is 2 or more
# Note: Regex IDs are in lower case and ' ' and '+' are changed into '_'
#------------------------------------------------------------------------------
@SearchEnginesSearchIDOrder_list1=(
# Japanese Search Engines
# Added by Ryu 2007.01.06
'66\.102\.11\.104',
'64\.233\.179\.99',
'72\.14\.235\.132',
'74\.125\.(77|95|153|155)\.132',
'74\.125\.113\.104',
'209\.85\.(129|135|165|173|175|207)\.104',
'209\.85\.(135|173|175|229)\.132',
'216\.239\.(37\.99|39\.104|41\.104|63\.104)',
'www\.answers\.com',
'search\.allabout\.co\.jp',
'search\.aol\.jp',
'search\.auone\.jp',
'avabox\.jp',
'biglobe\.ne\.jp',
'search\.cashback\.ne\.jp',
'search\.kbg\.jp',
'\.cometsystems\.com',
'\.cybozu\.net',
'cache\.yahoofs\.jp',
'clusty\.jp',
'eniro\.fi',
'excite\.co\.jp',
'excite-cache\.jp',
'ezsch\.ezweb\.ne\.jp',
'fc2\.super-search\.jp',
'find\.x0\.to',
'search\.fresheye\.com',
'dir\.fresheye\.com',
'goo\.ne\.jp',
'google\.co\.jp',
'search\.idol-photo\.com',
'iug\.newsing\.jp',
'search\.1pa2\.info',
'search\.juno\.com',
'jword\.jp',
's\.luna\.tv',
#'msnscache\.com',
'www\.marsflag\.com',
'myway\.com',
'naver\.co\.jp',
'naver\.com',
'search\.lifemile\.jp',
'search\.50pt\.jp',
'search\.point-box\.jp',
'sitesearch\.nikkei\.co\.jp',
'search\.okodukai\.jp',
'search\.jp\.aol\.com',
'search\.livedoor\.com',
'search\.nifty\.com',
'search\.msn\.co\.jp',
'search\.odn\.ne\.jp',
'search-hp\.com',
'olane\.jp',
'picsearch\.com',
'picsearch\.sk',
'www\.prsearch\.',
'websearch\.rakuten\.co\.jp',
'm\.technorati\.jp',
'www\.technorati\.jp',
'technorati\.jp',
'technorati\.com',
'tocc\.co\.jp',
'www\.baidu\.jp',
'image\.baidu\.jp',
'www\.ceek\.jp',
'www\.dir\.bg',
'www\.infoseek\.co\.jp',
'www\.megite\.com',
'www\.seochat\.com',
'www\.websearch\.com',
'www\.yahoogle\.jp',
# Major international search engines
'google\.[\w.]+/products',
'base\.google\.',
'froogle\.google\.',
'groups\.google\.',
'images\.google\.',
'google\.',
'googlee\.',
'googlecom\.com',
'goggle\.co\.hu',
'216\.239\.(35|37|39|51)\.100',
'216\.239\.(35|37|39|51)\.101',
'216\.239\.5[0-9]\.104',
'64\.233\.1[0-9]{2}\.104',
'66\.102\.[1-9]\.104',
'66\.249\.93\.104',
'72\.14\.2[0-9]{2}\.104',
'msn\.',
'live\.com',
'bing\.',
'voila\.',
'mindset\.research\.yahoo',
'yahoo\.','(66\.218\.71\.225|216\.109\.117\.135|216\.109\.125\.130|66\.218\.69\.11)',
'search\.aol\.co',
'tiscali\.',
'lycos\.',
'alexa\.com',
'alltheweb\.com',
'altavista\.',
'a9\.com',
'dmoz\.org',
'netscape\.',
'search\.terra\.',
'www\.search\.com',
'search\.sli\.sympatico\.ca',
'excite\.'
);

@SearchEnginesSearchIDOrder_list2=(
# Minor international search engines
'4\-counter\.com',
'att\.net',
'bungeebonesdotcom',
'northernlight\.',
'hotbot\.',
'kvasir\.',
'webcrawler\.',
'metacrawler\.',
'go2net\.com',
'(^|\.)go\.com',
'euroseek\.',
'looksmart\.',
'spray\.',
'nbci\.com\/search',
'de\.ask.\com', # break out Ask country specific engines.  (.jp is in Japan section)
'es\.ask.\com',
'fr\.ask.\com',
'it\.ask.\com',
'nl\.ask.\com',
'uk\.ask.\com',
'(^|\.)ask\.com',
'atomz\.',
'overture\.com',		# Replace 'goto\.com','Goto.com',
'teoma\.',
'findarticles\.com',
'infospace\.com',
'mamma\.',
'dejanews\.',
'dogpile\.com',
'wisenut\.com',
'ixquick\.com',
'search\.earthlink\.net',
'i-une\.com',
'blingo\.com',
'centraldatabase\.org',
'clusty\.com',
'mysearch\.',
'vivisimo\.com',
'kartoo\.com',
'icerocket\.com',
'sphere\.com',
'ledix\.net',
'start\.shaw\.ca',
'searchalot\.com',
'copernic\.com',
'avantfind\.com',
'steadysearch\.com',
'steady-search\.com',
# Chello Portals
'chello\.at',
'chello\.be',
'chello\.cz',
'chello\.fr',
'chello\.hu',
'chello\.nl',
'chello\.no',
'chello\.pl',
'chello\.se',
'chello\.sk',
'chello', # required as catchall for new countries not yet known
# Mirago
'mirago\.be',
'mirago\.ch',
'mirago\.de',
'mirago\.dk',
'es\.mirago\.com',
'mirago\.fr',
'mirago\.it',
'mirago\.nl',
'no\.mirago\.com',
'mirago\.se',
'mirago\.co\.uk',
'mirago', # required as catchall for new countries not yet known
'answerbus\.com',
'icq\.com\/search',
'nusearch\.com',
'goodsearch\.com',
'scroogle\.org',
'questionanswering\.com',
'mywebsearch\.com',
'as\.starware\.com',
# Social Bookmarking Services
'del\.icio\.us',
'digg\.com',
'stumbleupon\.com',
'swik\.net',
'segnalo\.alice\.it',
'ineffabile\.it',
# Minor Australian search engines
'anzwers\.com\.au',
# Minor brazilian search engines
'engine\.exe', 'miner\.bol\.com\.br',
# Minor chinese search engines
'\.baidu\.com',     # baidu search portal
'\.vnet\.cn',       # powered by MSN
'\.soso\.com',      # powered by Google
'\.sogou\.com',     # powered by Sohu
'\.3721\.com',      # powered by Yahoo!
'iask\.com',        # powered by Sina
'\.accoona\.com',   # Accoona
'\.163\.com',       # powered by Google
'\.zhongsou\.com',  # zhongsou search portal
# Minor czech search engines
'atlas\.cz','seznam\.cz','quick\.cz','centrum\.cz','jyxo\.(cz|com)','najdi\.to','redbox\.cz',
# Minor danish search-engines
'opasia\.dk', 'danielsen\.com', 'sol\.dk', 'jubii\.dk', 'find\.dk', 'edderkoppen\.dk', 'netstjernen\.dk', 'orbis\.dk', 'tyfon\.dk', '1klik\.dk', 'ofir\.dk',
# Minor dutch search engines
'ilse\.','vindex\.',
# Minor english search engines
'(^|\.)ask\.co\.uk','bbc\.co\.uk/cgi-bin/search','ifind\.freeserve','looksmart\.co\.uk','splut\.','spotjockey\.','ukdirectory\.','ukindex\.co\.uk','ukplus\.','searchy\.co\.uk',
# Minor finnish search engines
'haku\.www\.fi',
# Minor french search engines
'recherche\.aol\.fr','ctrouve\.','francite\.','\.lbb\.org','rechercher\.libertysurf\.fr', 'search[\w\-]+\.free\.fr', 'recherche\.club-internet\.fr',
'toile\.com', 'biglotron\.com',
'mozbot\.fr',
# Minor german search engines
'sucheaol\.aol\.de',
'fireball\.de','infoseek\.de','suche\d?\.web\.de','[a-z]serv\.rrzn\.uni-hannover\.de',
'suchen\.abacho\.de','brisbane\.t-online\.de','allesklar\.de','meinestadt\.de',
'212\.227\.33\.241',
'(161\.58\.227\.204|161\.58\.247\.101|212\.40\.165\.90|213\.133\.108\.202|217\.160\.108\.151|217\.160\.111\.99|217\.160\.131\.108|217\.160\.142\.227|217\.160\.176\.42)',
'wwweasel\.de',
'netluchs\.de',
'schoenerbrausen\.de',
# Minor Hungarian search engines
'heureka\.hu','vizsla\.origo\.hu','lapkereso\.hu','goliat\.hu','index\.hu','wahoo\.hu','webmania\.hu','search\.internetto\.hu',
'tango\.hu',
'keresolap\.hu',
'polymeta\.hu',
# Minor Indian search engines
'sify\.com',
# Minor Italian search engines
'virgilio\.it','arianna\.libero\.it','supereva\.com','kataweb\.it','search\.alice\.it\.master','search\.alice\.it','gotuneed\.com',
'godado','jumpy\.it','shinyseek\.it','teecno\.it',
# Minor Japanese search engines
'ask\.jp','sagool\.jp',
# Minor Norwegian search engines
'sok\.start\.no', 'eniro\.no',
# Minor Polish search engines
'szukaj\.wp\.pl','szukaj\.onet\.pl','dodaj\.pl','gazeta\.pl','gery\.pl','hoga\.pl','netsprint\.pl','interia\.pl','katalog\.onet\.pl','o2\.pl','polska\.pl','szukacz\.pl','wow\.pl',
# Minor russian search engines
'ya(ndex)?\.ru', 'aport\.ru', 'rambler\.ru', 'turtle\.ru', 'metabot\.ru',
# Minor Swedish search engines
'evreka\.passagen\.se','eniro\.se',
# Minor Slovak search engines
'zoznam\.sk',
# Minor Portuguese search engines
'sapo\.pt',
# Minor swiss search engines
'search\.ch', 'search\.bluewin\.ch',
# Minor Croatian, Serbian, Macedonian, Bosnian and Herzegovinian search engines
'pogodak\.'
);
@SearchEnginesSearchIDOrder_listgen=(
# Generic search engines
'search\..*\.\w+'
);


# NotSearchEnginesKeys
# If a search engine key is found, we check its exclude list to know if it's
# really a search engine
#------------------------------------------------------------------------------
%NotSearchEnginesKeys=(
'altavista\.'=>'babelfish\.altavista\.',
'google\.'=>'mail\.google\.',
'google\.'=>'translate\.google\.',
'msn\.'=>'hotmail\.msn\.',
'tiscali\.'=>'mail\.tiscali\.',
'yahoo\.'=>'mail\.yahoo\.',
'yandex\.'=>'direct\.yandex\.',
# For Japanese Search Engines
# Added by Ryu 2006.03.15
'infoseek\.co\.jp'=>'at\.imfoseek\.co\.jp',
'googlee\.'=>'mail\.googlee\.',
'googlee\.'=>'translate\.googlee\.',
'goo\.ne\.jp'=>'members\.goo\.ne\.jp'
);


# SearchEnginesHashID
# Each Search Engine Search ID is associated to an AWStats id string
#------------------------------------------------------------------------------
%SearchEnginesHashID = (
# Japanese Search Engines
# Added by Ryu 2007.01.06
'66\.102\.11\.104','googlejapan',
'64\.233\.179\.99','googlejapan',
'72\.14\.235\.132','google',
'74\.125\.(77|95|153|155)\.132','google',
'74\.125\.113\.104','google',
'209\.85\.(129|135|165|173|175|207)\.104','google',
'209\.85\.(135|173|175|229)\.132','google',
'216\.239\.(37\.99|39\.104|41\.104|63\.104)','googlejapan',
'www\.answers\.com','answers',
'search\.allabout\.co\.jp','allabout',
'search\.auone\.jp','auone',
'avabox\.jp','avabox',
'biglobe\.ne\.jp','biglobe',
'search\.cashback\.ne\.jp','cashback',
'search\.kbg\.jp','kbg',
'\.cometsystems\.com','cometsystems',
'\.cybozu\.net','cybozu',
'cache\.yahoofs\.jp','yahoocache',
'clusty\.jp','clustyjp',
'eniro\.fi','eniro',
'excite\.co\.jp','excitejapan',
'excite-cache\.jp','excitejapan',
'ezsch\.ezweb\.ne\.jp','ezweb',
'fc2\.super-search\.jp','fc2',
'find\.x0\.to','asamasearch',
'search\.fresheye\.com','fresheye',
'dir\.fresheye\.com','fresheyedir',
'goo\.ne\.jp','goo',
'google\.co\.jp','google',
'search\.idol-photo\.com','idolphoto',
'iug\.newsing\.jp','iug',
'search\.1pa2\.info','1pa2',
'search\.juno\.com','juno',
'jword\.jp','jword',
's\.luna\.tv','lunascape',
#'msnscache\.com','msn_cache',
'www\.marsflag\.com','marsflag',
'myway\.com','myway',
'naver\.co\.jp','naverjp',
'naver\.com','navercom',
'search\.lifemile\.jp','lifemile',
'search\.50pt\.jp','50pt',
'search\.point-box\.jp','pointbox',
'sitesearch\.nikkei\.co\.jp','nikkei',
'search\.okodukai\.jp','okodukai',
'search\.aol\.jp','aoljapan',
'search\.jp\.aol\.com','aoljapan',
'search\.livedoor\.com','livedoor',
'search\.nifty\.com','nifty',
'search\.msn\.co\.jp','msnjapan',
'search\.odn\.ne\.jp','odn',
'search-hp\.com','searchhp',
'olane\.jp','olane',
'picsearch\.com','picsearchcom',
'picsearch\.sk','picsearchsk',
'www\.prsearch\.','prsearch',
'websearch\.rakuten\.co\.jp','rakuten',
'm\.technorati\.jp','mtechnoratijp',
'www\.technorati\.jp','wtechnoratijp',
'technorati\.jp','technoratijp',
'technorati\.com','technorati',
'tocc\.co\.jp','tocc',
'www\.baidu\.jp','baidujp',
'image\.baidu\.jp','imagebaidujp',
'www\.ceek\.jp','ceekjp',
'www\.dir\.bg','dirbg',
'www\.infoseek\.co\.jp','infoseek',
'www\.megite\.com','megite',
'www\.seochat\.com','seochat',
'www\.websearch\.com','websearch',
'www\.yahoogle\.jp','yahoogle',
# Major international search engines
'google\.[\w.]+/products','google_products',
'base\.google\.','google_base',
'froogle\.google\.','google_froogle',
'groups\.google\.','google_groups',
'images\.google\.','google_image',
'google\.','google',
'googlee\.','google',
'googlecom\.com','google',
'goggle\.co\.hu','google',
'216\.239\.(35|37|39|51)\.100','google_cache',
'216\.239\.(35|37|39|51)\.101','google_cache',
'216\.239\.5[0-9]\.104','google_cache',
'64\.233\.1[0-9]{2}\.104','google_cache',
'66\.102\.[1-9]\.104','google_cache',
'66\.249\.93\.104','google_cache',
'72\.14\.2[0-9]{2}\.104','google_cache',
'msn\.','msn',
'live\.com','live',
'bing\.','bing',
'voila\.','voila',
'mindset\.research\.yahoo','yahoo_mindset',
'yahoo\.','yahoo','(66\.218\.71\.225|216\.109\.117\.135|216\.109\.125\.130|66\.218\.69\.11)','yahoo',
'lycos\.','lycos',
'alexa\.com','alexa',
'alltheweb\.com','alltheweb',
'altavista\.','altavista',
'a9\.com','a9',
'dmoz\.org','dmoz',
'netscape\.','netscape',
'search\.terra\.','terra',
'www\.search\.com','search.com',
'tiscali\.','tiscali',
'search\.aol\.co','aol',
'search\.sli\.sympatico\.ca','sympatico',
'excite\.','excite',
# Minor international search engines
'4\-counter\.com','google4counter',
'att\.net','att',
'bungeebonesdotcom','bungeebonesdotcom',
'northernlight\.','northernlight',
'hotbot\.','hotbot',
'kvasir\.','kvasir',
'webcrawler\.','webcrawler',
'metacrawler\.','metacrawler',
'go2net\.com','go2net',
'(^|\.)go\.com','go',
'euroseek\.','euroseek',
'looksmart\.','looksmart',
'spray\.','spray',
'nbci\.com\/search','nbci',
'de\.ask.\com','askde', # break out Ask country specific engines.
'es\.ask.\com','askes',
'fr\.ask.\com','askfr',
'it\.ask.\com','askit',
'nl\.ask.\com','asknl',
'uk\.ask.\com','askuk',
'(^|\.)ask\.co\.uk','askuk',
'(^|\.)ask\.com','ask',
'atomz\.','atomz',
'overture\.com','overture',		# Replace 'goto\.com','Goto.com',
'teoma\.','teoma',
'findarticles\.com','findarticles',
'infospace\.com','infospace',
'mamma\.','mamma',
'dejanews\.','dejanews',
'dogpile\.com','dogpile',
'wisenut\.com','wisenut',
'ixquick\.com','ixquick',
'search\.earthlink\.net','earthlink',
'i-une\.com','iune',
'blingo\.com','blingo',
'centraldatabase\.org','centraldatabase',
'clusty\.com','clusty',
'mysearch\.','mysearch',
'vivisimo\.com','vivisimo',
'kartoo\.com','kartoo',
'icerocket\.com','icerocket',
'sphere\.com','sphere',
'ledix\.net','ledix',
'start\.shaw\.ca','shawca',
'searchalot\.com','searchalot',
'copernic\.com','copernic',
'avantfind\.com','avantfind',
'steadysearch\.com','steadysearch',
'steady-search\.com','steadysearch',
# Chello Portals
'chello\.at','chelloat',
'chello\.be','chellobe',
'chello\.cz','chellocz',
'chello\.fr','chellofr',
'chello\.hu','chellohu',
'chello\.nl','chellonl',
'chello\.no','chellono',
'chello\.pl','chellopl',
'chello\.se','chellose',
'chello\.sk','chellosk',
'chello','chellocom',
# Mirago
'mirago\.be','miragobe',
'mirago\.ch','miragoch',
'mirago\.de','miragode',
'mirago\.dk','miragodk',
'es\.mirago\.com','miragoes',
'mirago\.fr','miragofr',
'mirago\.it','miragoit',
'mirago\.nl','miragonl',
'no\.mirago\.com','miragono',
'mirago\.se','miragose',
'mirago\.co\.uk','miragocouk',
'mirago','mirago', # required as catchall for new countries not yet known
'answerbus\.com','answerbus',
'icq\.com\/search','icq',
'nusearch\.com','nusearch',
'goodsearch\.com','goodsearch',
'scroogle\.org','scroogle',
'questionanswering\.com','questionanswering',
'mywebsearch\.com','mywebsearch',
'as\.starware\.com','comettoolbar',
# Social Bookmarking Services
'del\.icio\.us','delicious',
'digg\.com','digg',
'stumbleupon\.com','stumbleupon',
'swik\.net','swik',
'segnalo\.alice\.it','segnalo',
'ineffabile\.it','ineffabile',
# Minor Australian search engines
'anzwers\.com\.au','anzwers',
# Minor brazilian search engines
'engine\.exe','engine',
'miner\.bol\.com\.br','miner',
# Minor chinese search engines
'\.baidu\.com','baidu',
'iask\.com','iask',
'\.accoona\.com','accoona',
'\.3721\.com','3721',
'\.163\.com','netease',
'\.soso\.com','soso',
'\.zhongsou\.com','zhongsou',
'\.vnet\.cn','vnet',
'\.sogou\.com','sogou',
# Minor czech search engines
'atlas\.cz','atlas',
'seznam\.cz','seznam',
'quick\.cz','quick',
'centrum\.cz','centrum',
'jyxo\.(cz|com)','jyxo',
'najdi\.to','najdi',
'redbox\.cz','redbox',
# Minor danish search-engines
'opasia\.dk','opasia',
'danielsen\.com','danielsen',
'sol\.dk','sol',
'jubii\.dk','jubii',
'find\.dk','finddk',
'edderkoppen\.dk','edderkoppen',
'netstjernen\.dk','netstjernen',
'orbis\.dk','orbis',
'tyfon\.dk','tyfon',
'1klik\.dk','1klik',
'ofir\.dk','ofir',
# Minor dutch search engines
'ilse\.','ilse',
'vindex\.','vindex',
# Minor english search engines
'bbc\.co\.uk/cgi-bin/search','bbc',
'ifind\.freeserve','freeserve',
'looksmart\.co\.uk','looksmartuk',
'splut\.','splut',
'spotjockey\.','spotjockey',
'ukdirectory\.','ukdirectory',
'ukindex\.co\.uk','ukindex',
'ukplus\.','ukplus',
'searchy\.co\.uk','searchy',
# Minor finnish search engines
'haku\.www\.fi','haku',
# Minor french search engines
'recherche\.aol\.fr','aolfr',
'ctrouve\.','ctrouve',
'francite\.','francite',
'\.lbb\.org','lbb',
'rechercher\.libertysurf\.fr','libertysurf',
'search[\w\-]+\.free\.fr','free',
'recherche\.club-internet\.fr','clubinternet',
'toile\.com','toile',
'biglotron\.com', 'biglotron',
'mozbot\.fr', 'mozbot',
# Minor german search engines
'sucheaol\.aol\.de','aolde',
'fireball\.de','fireball',
'infoseek\.de','infoseek',
'suche\d?\.web\.de','webde',
'[a-z]serv\.rrzn\.uni-hannover\.de','meta',
'suchen\.abacho\.de','abacho',
'brisbane\.t-online\.de','t-online',
'allesklar\.de','allesklar',
'meinestadt\.de','meinestadt',
'212\.227\.33\.241','metaspinner',
'(161\.58\.227\.204|161\.58\.247\.101|212\.40\.165\.90|213\.133\.108\.202|217\.160\.108\.151|217\.160\.111\.99|217\.160\.131\.108|217\.160\.142\.227|217\.160\.176\.42)','metacrawler_de',
'wwweasel\.de','wwweasel',
'netluchs\.de','netluchs',
'schoenerbrausen\.de','schoenerbrausen',
# Minor Hungarian search engines
'heureka\.hu','heureka',
'vizsla\.origo\.hu','origo',
'lapkereso\.hu','lapkereso',
'goliat\.hu','goliat',
'index\.hu','indexhu',
'wahoo\.hu','wahoo',
'webmania\.hu','webmania',
'search\.internetto\.hu','internetto',
'tango\.hu','tango_hu',
'keresolap\.hu','keresolap_hu',
'polymeta\.hu','polymeta_hu',
# Minor Indian search engines
'sify\.com','sify',
# Minor Italian search engines
'virgilio\.it','virgilio',
'arianna\.libero\.it','arianna',
'supereva\.com','supereva',
'kataweb\.it','kataweb',
'search\.alice\.it\.master','aliceitmaster',
'search\.alice\.it','aliceit',
'gotuneed\.com','gotuneed',
'godado','godado',
'jumpy\.it','jumpy\.it',
'shinyseek\.it','shinyseek\.it',
'teecno\.it','teecnoit',
# Minor Japanese search engines
'ask\.jp','askjp',
'sagool\.jp','sagool',
# Minor Norwegian search engines
'sok\.start\.no','start', 'eniro\.no','eniro',
# Minor Polish search engines
'szukaj\.wp\.pl','wp',
'szukaj\.onet\.pl','onetpl',
'dodaj\.pl','dodajpl',
'gazeta\.pl','gazetapl',
'gery\.pl','gerypl',
'netsprint\.pl\/hoga\-search','hogapl',
'netsprint\.pl','netsprintpl',
'interia\.pl','interiapl',
'katalog\.onet\.pl','katalogonetpl',
'o2\.pl','o2pl',
'polska\.pl','polskapl',
'szukacz\.pl','szukaczpl',
'wow\.pl','wowpl',
# Minor russian search engines
'ya(ndex)?\.ru','yandex',
'aport\.ru','aport',
'rambler\.ru','rambler',
'turtle\.ru','turtle',
'metabot\.ru','metabot',
# Minor Swedish search engines
'evreka\.passagen\.se','passagen',
'eniro\.se','enirose',
# Minor Slovak search engines
'zoznam\.sk','zoznam',
# Minor Portuguese search engines
'sapo\.pt','sapo',
# Minor swiss search engines
'search\.ch','searchch',
'search\.bluewin\.ch','bluewin',
# Minor Croatian, Serbian, Macedonian, Bosnian and Herzegovinian search engines
'pogodak\.','pogodak',
# Generic search engines
'search\..*\.\w+','search'
);


# SearchEnginesWithKeysNotInQuery
# List of search engines that store keyword as page instead of query parameter
#------------------------------------------------------------------------------
%SearchEnginesWithKeysNotInQuery=(
'a9',1,   # www.a9.com/searckey1%20searchkey2
'answers',1,	# www.answers.com/searckey1%20searchkey2
'fresheyedir',1,	# dir.fresheye.com/ds/kw/new/searckey1/
'idolphoto',1,	# search.idol-photo.com/%E3%82%A8%E3%83%AB%E3%83%95/index10.html
'iug',1,	# iug.newsing.jp/tag/searckey1?...
'megite',1,	# www.megite.com/discover/searckey1%20searchkey2
'olane',1,	# olane.jp/keyword/eラーニング/c/8
'yahoogle',1,   # www.yahoogle.jp/yahoogle-1-searckey1%20searchkey2
'mtechnoratijp',1,  # m.technorati.jp/search/searckey1+searchkey2
'wtechnoratijp',1,	# www.technorati.jp/search/searckey1+searchkey2
			# www.technorati.jp/posts/tag/searckey1+searchkey2
'technoratijp',1,  # technorati.jp/search/searckey1+searchkey2
'technorati',1,  # technorati.com/posts/tag/searckey1+searchkey2
);

# SearchEnginesKnownUrl
# Known rules to extract keywords from a referrer search engine URL
#------------------------------------------------------------------------------
%SearchEnginesKnownUrl=(
# Japanese Search Engines
# Added by Ryu 2007.01.06
'answers','www\.answers\.com\/',
'asamasearch','q=',
'askjapan','q=',
'allabout','qs=',
'auone','q=',
'avabox','keyword=',
'baidujp','wd=',
'imagebaidujp','word=',
'biglobe','q=',
'cashback','keywords=',
'kbg','extrakey=',
'cometsystems','qry=',
'cybozu','(keywords|query)=',
'ceekjp','q=',
'dirbg','s=',
'clustyjp','query=',
'eniro','q=',
'excitejapan','search=',
'ezweb','query=',
'fc2','search=',
'fresheye','kw=',
'fresheyedir','dir\.fresheye\.com\/ds\/kw\/new\/',
'goo','mt=',
'googlejapan','(p=|q=cache:[0-9A-Za-z]{12}:|as_p=|as_q=|as_epq=|q=)',
'infoseek','qt=',
'idolphoto','photo\.com\/',
'iug','iug\.newsing\.jp\/tag\/',
'1pa2','feed=',
'juno','query=',
'jword','(name|q)=',
'livedoor','q=',
'lunascape','q=',
'megite','www\.megite\.com\/discover\/',
'marsflag','key=',
'myway','searchfor=',
'msnjapan','(q|mt)=',
'naverjp','query=',
'navercom','query=',
'lifemile','keywords=',
'50pt','keywords=',
'pointbox','keywords=',
'nikkei','q=',
'okodukai','q=',
'aoljapan','(query|query_contain)=',
'nifty','(text|q)=',
'odn','querystring=',
'olane','keyword\/',
'searchhp','kensaku_ward=',
'picsearchcom','q=',
'picsearchsk','q=',
'prsearch','query=',
'rakuten','qt=',
'mtechnoratijp','m\.technorati\.jp\/search\/',
'wtechnoratijp','www\.technorati\.jp\/(?:search|posts/tag)\/',
'technoratijp','technorati\.jp\/search\/',
'technorati','technorati\.com\/(?:tag|posts/tag)\/',
'tocc','qry=',
'yahoocache','w=',
'websearch','qkw=',
'yahoogle','www\.yahoogle\.jp\/yahoogle-1-',
# Most common search engines
'alexa','q=',
'alltheweb','q(|uery)=',
'altavista','q=',
'a9','a9\.com\/',
'dmoz','search=',
'google_products','(p|q|as_p|as_q)=',
'google_base','(p|q|as_p|as_q)=',
'google_froogle','(p|q|as_p|as_q)=',
'google_groups','group\/', # does not work
'google_image','(p|q|as_p|as_q)=',
'google_cache','(p|q|as_p|as_q)=cache:[0-9A-Za-z]{12}:',
'google','(p=|as_p=|as_q=|as_epq=|q=cache:[0-9A-Za-z]{12}:|q=)',
'lycos','query=',
'msn','(q|mt)=',
'live','q=',
'bing','q=',
'netscape','search=',
'tiscali','key=',
'aol','query=',
'terra','query=',
'voila','(kw|rdata)=',
'search.com','q=',
'yahoo_mindset','p=',
'yahoo','(p|kw)=',
'sympatico', 'query=',
'excite','search=',
# Minor international search engines
'google4counter','(p|q|as_p|as_q)=',
'att','qry=',
'bungeebonesdotcom','query=',
'go','qt=',
'askde','(ask|q)=', # break out Ask country specific engines.
'askes','(ask|q)=',
'askfr','(ask|q)=',
'askit','(ask|q)=',
'asknl','(ask|q)=',
'ask','(ask|q)=',
'atomz','sp-q=',
'euroseek','query=',
'findarticles','key=',
'go2net','general=',
'hotbot','mt=',
'infospace','qkw=',
'kvasir', 'q=',
'looksmart','key=',
'mamma','query=',
'metacrawler','general=',
'nbci','keyword=',
'northernlight','qr=',
'overture','keywords=',
'dogpile', 'q(|kw)=',
'spray','string=',
'teoma','q=',
'webcrawler','searchText=',
'wisenut','query=',
'ixquick', 'query=',
'earthlink', 'q=',
'iune','(keywords|q)=',
'blingo','q=',
'centraldatabase','query=',
'clusty','query=',
'mysearch','searchfor=',
'vivisimo','query=',
# kartoo: No keywords passed in referring URL.
'kartoo','',
'icerocket','q=',
'sphere','q=',
'ledix','q=',
'shawca','q=',
'searchalot','q=',
'copernic','web\/',
'avantfind','keywords=',
'steadysearch','w=',
# Chello Portals
'chelloat','q1=',
'chellobe','q1=',
'chellocz','q1=',
'chellofr','q1=',
'chellohu','q1=',
'chellonl','q1=',
'chellono','q1=',
'chellopl','q1=',
'chellose','q1=',
'chellosk','q1=',
'chellocom','q1=',
# Mirago
'miragobe','(txtsearch|qry)=',
'miragoch','(txtsearch|qry)=',
'miragode','(txtsearch|qry)=',
'miragodk','(txtsearch|qry)=',
'miragoes','(txtsearch|qry)=',
'miragofr','(txtsearch|qry)=',
'miragoit','(txtsearch|qry)=',
'miragonl','(txtsearch|qry)=',
'miragono','(txtsearch|qry)=',
'miragose','(txtsearch|qry)=',
'miragocouk','(txtsearch|qry)=',
'mirago','(txtsearch|qry)=',
'answerbus','', # Does not provide query parameters
'icq','q=',
'nusearch','nusearch_terms=',
'goodsearch','Keywords=',
'scroogle','Gw=', # Does not always provide query parameters
'questionanswering','',
'mywebsearch','searchfor=',
'comettoolbar','qry=',
# Social Bookmarking Services
'delicious','all=',
'digg','s=',
'stumbleupon','',
'swik','swik\.net/', # does not work. Keywords follow domain, e.g. http://swik.net/awstats+analytics
'segnalo','',
'ineffabile','',
# Minor Australian search engines
'anzwers','search=',
# Minor brazilian search engines
'engine','p1=', 'miner','q=',
# Minor chinese search engines
'baidu','(wd|word)=',
'iask','(w|k)=',
'accoona','qt=',
'3721','(p|name)=',
'netease','q=',
'soso','q=',
'zhongsou','(word|w)=',
'sogou', 'query=',
'vnet','kw=',
# Minor czech search engines
'atlas','searchtext=', 'seznam','w=', 'quick','query=', 'centrum','q=', 'jyxo','s=', 'najdi','dotaz=', 'redbox','srch=',
# Minor danish search engines
'opasia','q=', 'danielsen','q=', 'sol','q=', 'jubii','soegeord=', 'finddk','words=', 'edderkoppen','query=', 'orbis','search_field=', '1klik','query=', 'ofir','querytext=',
# Minor dutch search engines
'ilse','search_for=', 'vindex','in=',
# Minor english search engines
'askuk','(ask|q)=', 'bbc','q=', 'freeserve','q=', 'looksmartuk','key=',
'splut','pattern=', 'spotjockey','Search_Keyword=', 'ukindex', 'stext=', 'ukdirectory','k=', 'ukplus','search=', 'searchy', 'search_term=',
# Minor finnish search engines
'haku','w=',
# Minor french search engines
'francite','name=', 'clubinternet', 'q=',
'toile', 'q=',
'biglotron','question=',
'mozbot','q=',
# Minor german search engines
'aolde','q=',
'fireball','q=', 'infoseek','qt=', 'webde','su=',
'abacho','q=', 't-online','q=',
'metaspinner','qry=',
'metacrawler_de','qry=',
'wwweasel','q=',
'netluchs','query=',
'schoenerbrausen','q=',
# Minor Hungarian search engines
'heureka','heureka=', 'origo','(q|search)=', 'goliat','KERESES=', 'wahoo','q=', 'internetto','searchstr=',
'keresolap_hu','q=',
'tango_hu','q=',
'polymeta_hu','',
# Minor Indian search engines
'sify','keyword=',
# Minor Italian search engines
'virgilio','qs=',
'arianna','query=',
'supereva','q=',
'kataweb','q=',
'aliceitmaster','qs=',
'aliceit','qs=',
'gotuneed','', # Not yet known
'godado','Keywords=',
'jumpy\.it','searchWord=',
'shinyseek\.it','KEY=',
'teecnoit','q=',
# Minor Japanese search engines
'askjp','(ask|q)=',
'sagool','q=',
# Minor Norwegian search engines
'start','q=', 'eniro','q=',
# Minor Polish search engines
'wp','szukaj=',
'onetpl','qt=',
'dodajpl','keyword=',
'gazetapl','slowo=',
'gerypl','q=',
'hogapl','qt=',
'netsprintpl','q=',
'interiapl','q=',
'katalogonetpl','qt=',
'o2pl','qt=',
'polskapl','qt=',
'szukaczpl','q=',
'wowpl','q=',
# Minor russian search engines
'yandex', 'text=', 'rambler','words=', 'aport', 'r=', 'metabot', 'st=',
# Minor swedish search engines
'passagen','q=',
'enirose','q=',
# Minor swiss search engines
'searchch', 'q=', 'bluewin', 'qry=',
# Minor Croatian, Serbian, Macedonian, Bosnian and Herzegovinian search engines
'pogodak', 'q='
);

# SearchEnginesKnownUrlTrail
# Known rules to extract keywords from a referrer search engine URL
#------------------------------------------------------------------------------
%SearchEnginesKnownUrlTrail=(
# Japanese Search Engines
# Added by Hobbit_makoto 2007.01.17
'fresheyedir','\/',
'idolphoto','\/index[0-9]+\.html',
'olane','\/c\/\d',
'yahoogle','\.html',
);

# SearchEnginesKnownUrlNotFound
# Known rules to extract not found keywords from a referrer search engine URL
#------------------------------------------------------------------------------
%SearchEnginesKnownUrlNotFound=(
# Most common search engines
'msn','origq='
);

# If no rules are known, WordsToExtractSearchUrl will be used to search keyword parameter
# If no rules are known and search in WordsToExtractSearchUrl failed, this will be used to clean URL of not keyword parameters.
#------------------------------------------------------------------------------
@WordsToExtractSearchUrl= ('tn=','ie=','ask=','claus=','general=','key=','kw=','keyword=','keywords=','MT=','p=','q=','qr=','qt=','query=','s=','search=','searchText=','string=','su=','txtsearch=','w=');
# Words after "look" are added by Ryu for Japanese Search Engines.
@WordsToCleanSearchUrl= ('act=','annuaire=','btng=','cat=','categoria=','cfg=','cof=','cou=','count=','cp=','dd=','domain=','dt=','dw=','enc=','exec=','geo=','hc=','height=','hits=','hl=','hq=','hs=','id=','kl=','lang=','loc=','lr=','matchmode=','medor=','message=','meta=','mode=','order=','page=','par=','pays=','pg=','pos=','prg=','qc=','refer=','sa=','safe=','sc=','sort=','src=','start=','style=','stype=','sum=','tag=','temp=','theme=','type=','url=','user=','width=','what=','\\.x=','\\.y=','y=','look=','target=','collection=','showSummary=','perPage=','next=','DB=','OPE=','Max=','base=','submit=','SearchType=','SESSIONID=','QUERYRULE=','DISPLAYMIN=','RELURLSWITCH=','SORT=','start=','direct=','relwd=','lk=','svx=','nh=','internet=','DC=','submit0=','DEST=','where=','CCM=','NRS=','UNI=','UD0=','FRS=','sv=','rf=','oq=','col=','act\.search=');

# SearchEnginesKnownUTFCoding
# Known parameter that proves a search engine has coded its parameters in UTF-8
#------------------------------------------------------------------------------
%SearchEnginesKnownUTFCoding=(
# Most common search engines
'google','ie=utf-8',
'alltheweb','cs=utf-8'
);


# SearchEnginesHashLib
# List of search engines names
# 'search_engine_id', 'search_engine_name',
#------------------------------------------------------------------------------
%SearchEnginesHashLib=(
# Japanese Search Engines
# Added by Ryu 2006.03.15
'answers','<a href="http://www.answers.com">Answers.com</a>',
'allabout','<a href="http://search.allabout.co.jp">All About</a>',
'auone','<a href="http://search.auone.jp">au one</a>',
'avabox','<a href="http://avabox.jp">Avabox</a>',
'googlejapan','<a href="http://www.google.co.jp">Google Japan</a>',
'biglobe','<a href="http://www.biglobe.ne.jp">BIGLOBE</a>',
'cashback','<a href="http://www.cashback.ne.jp">Cashback Mall</a>',
'kbg','<a href="http://keitai.biglobe.ne.jp">ケータイBIGLOBE</a>',
'cometsystems','Comet Systems',
'cybozu','<a href="http://www.cybozu.net">cybozu.net</a>',
'aoljapan','<a href="http://search.jp.aol.com">AOL Japan</a>',
'goo','<a href="http://goo.ne.jp">Goo</a>',
'nifty','<a href="http://www.nifty.com/search/">Nifty</a>',
'msnjapan','<a href="http://jp.msn.com">Microsoft MSN Japan</a>',
'odn','ODN',
'clustyjp','<a href="http://clusty.jp">Clusty Japan</a>',
'eniro','<a href="http://eniro.fi">eniro</a>',
'excitejapan','Excite Japan',
'ezweb','<a href="http://www.au.kddi.com/service/ezweb/index.html">ezweb</a>',
'mtechnoratijp','<a href="http://m.technorati.jp">テクノラティモバイル検索</a>',
'wtechnoratijp','<a href="http://technorati.jp">テクノラティ検索(WWW)</a>',
'technoratijp','<a href="http://technorati.jp">テクノラティ検索</a>',
'technorati','<a href="http://technorati.com">Technorati Search</a>',
'websearch','<a href="http://www.websearch.com">WebSearch</a>',
'yahoogle','<a href="http://www.yahoogle.jp">yahoogle</a>',
#'msn_cache','MSN Cache',
'marsflag','<a href="http://www.marsflag.com">MARSFLAG</a>',
'myway','<a href="http://home.myway.com">MyWay</a>',
'naverjp','<a href="http://www.naver.co.jp">Naver.co.jp</a>',
'navercom','<a href="http://www.naver.com">Naver.com</a>',
'nikkei','<a href="http://sitesearch.nikkei.co.jp">Nikkei Net</a>',
'lifemile','<a href="http://search.lifemile.jp">Life Mile</a>',
'50pt','<a href="http://search.50pt.jp">50pt</a>',
'pointbox','<a href="http://search.point-box.jp">Point Box</a>',
'okodukai','<a href="http://search.okodukai.jp">お小遣いJP</a>',
'asamasearch','<a href="http://find.x0.to">Asamasearch</a>',
'fc2','<a href="http://fc2.super-search.jp/">fc2.super-search.jp</a>',
'fresheye','<a href="http://fresheye.com">Fresheye</a>',
'fresheyedir','<a href="http://fresheye.com">Fresheye(Dir)</a>',
'idolphoto','<a href="http://search.idol-photo.com">search.idol-photo.com</a>',
'iug','<a href="http://iug.newsing.jp">iUG-newsing</a>',
'1pa2','<a href="http://search.1pa2.info">1pa2</a>',
'juno','<a href="http://search.juno.com">JUNO</a>',
'jword','<a href="http://www.jword.jp">Jword</a>',
'lunascape','<a href="http://s.luna.tv">Lunascape</a>',
'seochat','SEOChat',
'tocc','TOCC/Search',
'searchhp','<a href="http://search-hp.com">Search HP</a>',
'olane','<a href="http://olane.jp">olane.jp</a>',
'picsearchcom','<a href="www.picsearch.com">picsearch</a>',
'picsearchsk','<a href="www.picsearch.sk">picsearch.sk</a>',
'prsearch','<a href="http://www.prsearch.biz">prsearch</a>',
'rakuten','<a href="http://www.rakuten.co.jp">楽天</a>',
'baidujp','<a href="http://www.baidu.jp">baidu.jp</a>',
'imagebaidujp','<a href="http://image.baidu.jp">image.baidu.jp</a>',
'ceekjp','<a href="http://www.ceek.jp">CEEK.JP</a>',
'dirbg','<a href="http://dir.dir.bg">Dir.bg</a>',
'infoseek','<a href="http://www.infoseek.co.jp">Infoseek</a>',
'yahoocache','Yahoo Cache',
'askjapan','Ask Jeeves Japan',
'livedoor','Livedoor',
# Major international search engines
'alexa','<a href="http://www.alexa.com" title="Search Engine Home Page">Alexa</a>',
'alltheweb','<a href="http://www.alltheweb.com" title="Search Engine Home Page">AllTheWeb</a>',
'altavista','<a href="http://www.altavista.com" title="Search Engine Home Page">AltaVista</a>',
'a9', '<a href="http://www.a9.com" title="Search Engine Home Page">A9</a>',
'dmoz','<a href="http://dmoz.org" title="Search Engine Home Page">DMOZ</a>',
'google_products','<a href="http://www.google.com/products" title="Search Engine Home Page">Google (Products)</a>',
'google_base','<a href="http://base.google.com" title="Search Engine Home Page">Google (Base)</a>',
'google_froogle','<a href="http://froogle.google.com" title="Search Engine Home Page">Froogle (Google)</a>',
'google_groups','<a href="http://groups.google.com" title="Search Engine Home Page">Google (Groups)</a>',
'google_image','<a href="http://images.google.com" title="Search Engine Home Page">Google (Images)</a>',
'google_cache','<a href="http://www.google.com/help/features.html#cached" title="Search Engine Home Page">Google (cache)</a>',
'google','<a href="http://www.google.com" title="Search Engine Home Page">Google</a>',
'lycos','<a href="http://www.lycos.com" title="Search Engine Home Page">Lycos</a>',
'msn','<a href="http://search.msn.com" title="Search Engine Home Page">Microsoft MSN Search</a>',
'live','<a href="http://www.live.com" title="Search Engine Home Page">Microsoft Windows Live</a>',
'bing','<a href="http://www.bing.com" title="Search Engine Home Page">Microsoft Bing</a>',
'netscape','<a href="http://www.netscape.com" title="Search Engine Home Page">Netscape</a>',
'aol','<a href="http://www.aol.com" title="Search Engine Home Page">AOL</a>',
'terra','<a href="http://www.terra.es" title="Search Engine Home Page">Terra</a>',
'tiscali','<a href="http://search.tiscali.com" title="Search Engine Home Page">Tiscali</a>',
'voila','<a href="http://www.voila.fr" title="Search Engine Home Page">Voila</a>',
'search.com','<a href="http://www.search.com" title="Search Engine Home Page">Search.com</a>',
'yahoo_mindset','<a href="http://mindset.research.yahoo.com" title="Search Engine Home Page">Yahoo! Mindset</a>',
'yahoo','<a href="http://www.yahoo.com" title="Search Engine Home Page">Yahoo!</a>',
'sympatico','<a href="http://sympatico.msn.ca" title="Search Engine Home Page">Sympatico</a>',
'excite','<a href="http://www.excite.com" title="Search Engine Home Page">Excite</a>',
# Minor international search engines
'google4counter','<a href="http://www.4-counter.com" title="Search Engine Home Page">4-counter (Google)</a>',
'att','<a href="http://www.att.net" title="Search Engine Home Page">AT&amp;T search (powered by Google)</a>',
'bungeebonesdotcom','<a href="http://BungeeBones.com/search.php" title="Search Engine Home Page">BungeeBones</a>',
'go','Go.com',
'askde','<a href="http://de.ask.com" title="Search Engine Home Page">Ask Deutschland</a>',
'askes','<a href="http://es.ask.com" title="Search Engine Home Page">Ask Espa&ntilde;a</a>', # break out Ask country specific engines.
'askfr','<a href="http://fr.ask.com" title="Search Engine Home Page">Ask France</a>',
'askit','<a href="http://it.ask.com" title="Search Engine Home Page">Ask Italia</a>',
'asknl','<a href="http://nl.ask.com" title="Search Engine Home Page">Ask Nederland</a>',
'ask','<a href="http://www.ask.com" title="Search Engine Home Page">Ask</a>',
'atomz','Atomz',
'dejanews','DejaNews',
'euroseek','Euroseek',
'findarticles','Find Articles',
'go2net','Go2Net (Metamoteur)',
'hotbot','Hotbot',
'infospace','InfoSpace',
'kvasir','Kvasir',
'looksmart','Looksmart',
'mamma','Mamma',
'metacrawler','MetaCrawler (Metamoteur)',
'nbci','NBCI',
'northernlight','NorthernLight',
'overture','Overture',                 # Replace 'goto\.com','Goto.com',
'dogpile','<a href="http://www.dogpile.com" title="Search Engine Home Page">Dogpile</a>',
'spray','Spray',
'teoma','<a href="http://search.ask.com" title="Search Engine Home Page">Teoma</a>',							# Replace 'directhit\.com','DirectHit',
'webcrawler','<a href="http://www.webcrawler.com" title="Search Engine Home Page">WebCrawler</a>',
'wisenut','WISENut',
'ixquick','<a href="http://www.ixquick.com" title="Search Engine Home Page">ix quick</a>',
'earthlink', 'Earth Link',
'iune','<a href="http://www.i-une.com" title="Search Engine Home Page">i-une</a>',
'blingo','<a href="http://www.blingo.com" title="Search Engine Home Page">Blingo</a>',
'centraldatabase','<a href="http://search.centraldatabase.org" title="Search Engine Home Page">GPU p2p search</a>',
'clusty','<a href="http://www.clusty.com" title="Search Engine Home Page">Clusty</a>',
'mysearch','<a href="http://www.mysearch.com" title="Search Engine Home Page">My Search</a>',
'vivisimo','<a href="http://www.vivisimo.com" title="Search Engine Home Page">Vivisimo</a>',
'kartoo','<a href="http://www.kartoo.com" title="Search Engine Home Page">Kartoo</a>',
'icerocket','<a href="http://www.icerocket.com" title="Search Engine Home Page">Icerocket (Blog)</a>',
'sphere','<a href="http://www.sphere.com" title="Search Engine Home Page">Sphere (Blog)</a>',
'ledix','<a href="http://www.ledix.net" title="Search Engine Home Page">Ledix</a>',
'shawca','<a href="http://start.shaw.ca" title="Search Engine Home Page">Shaw.ca</a>',
'searchalot','<a href="http://www.searchalot.com" title="Search Engine Home Page">Searchalot</a>',
'copernic','<a href="http://www.copernic.com" title="Search Engine Home Page">Copernic</a>',
'avantfind','<a href="http://www.avantfind.com" title="Search Engine Home Page">Avantfind</a>',
'steadysearch','<a href="http://www.avantfind.com" title="Search Engine Home Page">Avantfind</a>',
# Chello Portals
'chelloat','<a href="http://www.chello.at" title="Search Engine Home Page">Chello Austria</a>',
'chellobe','<a href="http://www.chello.be" title="Search Engine Home Page">Chello Belgium</a>',
'chellocz','<a href="http://www.chello.cz" title="Search Engine Home Page">Chello Czech Republic</a>',
'chellofr','<a href="http://www.chello.fr" title="Search Engine Home Page">Chello France</a>',
'chellohu','<a href="http://www.chello.hu" title="Search Engine Home Page">Chello Hungary</a>',
'chellonl','<a href="http://www.chello.nl" title="Search Engine Home Page">Chello Netherlands</a>',
'chellono','<a href="http://www.chello.no" title="Search Engine Home Page">Chello Norway</a>',
'chellopl','<a href="http://www.chello.pl" title="Search Engine Home Page">Chello Poland</a>',
'chellose','<a href="http://www.chello.se" title="Search Engine Home Page">Chello Sweden</a>',
'chellosk','<a href="http://www.chello.sk" title="Search Engine Home Page">Chello Slovakia</a>',
'chellocom','<a href="http://www.chello.com" title="Search Engine Home Page">Chello (Country not recognized)</a>',
# Mirago
'miragobe','<a href="http://www.mirago.be" title="Search Engine Home Page">Mirago Belgium</a>',
'miragoch','<a href="http://www.mirago.ch" title="Search Engine Home Page">Mirago Switzerland</a>',
'miragode','<a href="http://www.mirago.de" title="Search Engine Home Page">Mirago Germany</a>',
'miragodk','<a href="http://www.mirago.dk" title="Search Engine Home Page">Mirago Denmark</a>',
'miragoes','<a href="http://es.mirago.com" title="Search Engine Home Page">Mirago Spain</a>',
'miragofr','<a href="http://www.mirago.fr" title="Search Engine Home Page">Mirago France</a>',
'miragoit','<a href="http://www.mirago.it" title="Search Engine Home Page">Mirago Italy</a>',
'miragonl','<a href="http://www.mirago.nl" title="Search Engine Home Page">Mirago Netherlands</a>',
'miragono','<a href="http://no.mirago.com" title="Search Engine Home Page">Mirago Norway</a>',
'miragose','<a href="http://www.mirago.se" title="Search Engine Home Page">Mirago Sweden</a>',
'miragocouk','<a href="http://zone.mirago.co.uk" title="Search Engine Home Page">Mirago UK</a>',
'mirago','<a href="http://www.mirago.com" title="Search Engine Home Page">Mirago (country unknown)</a>',
'answerbus','<a href="http://www.answerbus.com" title="Search Engine Home Page">Answerbus</a>',
'icq','<a href="http://www.icq.com" title="Search Engine Home Page">icq</a>',
'nusearch','<a href="http://www.nusearch.com" title="Search Engine Home Page">Nusearch</a>',
'goodsearch','<a href="http://www.goodsearch.com" title="Search Engine Home Page">GoodSearch</a>',
'scroogle','<a href="http://www.scroogle.org" title="Search Engine Home Page">Scroogle</a>',
'questionanswering','<a href="http://www.questionanswering.com" title="Questionanswering home page">Questionanswering</a>',
'mywebsearch','<a href="http://search.mywebsearch.com" title="MyWebSearch home page">MyWebSearch</a>',
'comettoolbar','<a href="http://as.starware.com/dp/search" title="Comet toolbar search home page">Comet toolbar search</a>',
# Social Bookmarking Services
'delicious','<a href="http://del.icio.us" title="del.icio.us home page">del.icio.us</a> (Social Bookmark)',
'digg','<a href="http://www.digg.com" title="Digg home page">Digg</a> (Social Bookmark)',
'stumbleupon','<a href="http://www.stumbleupon.com" title="Stumbleupon home page">Stumbleupon</a> (Social Bookmark)',
'swik','<a href="http://swik.net" title="Swik home page">Swik</a> (Social Bookmark)',
'segnalo','<a href="http://segnalo.alice.it" title="Segnalo home page">Segnalo</a> (Social Bookmark)',
'ineffabile','<a href="http://www.ineffabile.it" title="Ineffabile.it home page">Ineffabile.it</a> (Social Bookmark)',
# Minor Australian search engines
'anzwers','<a href="http://anzwers.com.au" title="anzwers.com.au home page">anzwers.com.au</a>',
# Minor brazilian search engines
'engine','Cade', 'miner','Meta Miner',
# Minor chinese search engines
'baidu','<a href="http://www.baidu.com">Baidu</a>',
'iask','<a href="http://www.iask.com">Iask</a>',
'accoona','<a href="http://cn.accoona.com">Accoona</a>',
'3721','<a href="http://www.3721.com">3721</a>',
'netease', '<a href="http://www.163.com">NetEase</a>',
'soso','<a href="http://www.soso.com">SoSo</a>',
'zhongsou','<a href="http://www.zhongsou.com">ZhongSou</a>',
'sogou', '<a href="http://www.sogou.com">SoGou</a>',
'vnet','<a href="http://114.vnet.cn">VNet</a>',
# Minor czech search engines
'atlas','Atlas.cz',	'seznam','Seznam', 'quick','Quick.cz', 'centrum','Centrum.cz', 'jyxo','Jyxo.cz', 'najdi','Najdi.to', 'redbox','RedBox.cz',
# Minor danish search-engines
'opasia','Opasia', 'danielsen','Thor (danielsen.com)', 'sol','SOL', 'jubii','Jubii', 'finddk','Find', 'edderkoppen','Edderkoppen', 'netstjernen','Netstjernen', 'orbis','Orbis', 'tyfon','Tyfon', '1klik','1Klik', 'ofir','Ofir',
# Minor dutch search engines
'ilse','Ilse','vindex','Vindex\.nl',
# Minor english search engines
'askuk','<a href="http://uk.ask.com" title="Search Engine Home Page">Ask UK</a>',
'bbc','BBC', 'freeserve','Freeserve', 'looksmartuk','Looksmart UK',
'splut','Splut', 'spotjockey','Spotjockey', 'ukdirectory','UK Directory', 'ukindex','UKIndex', 'ukplus','UK Plus', 'searchy','searchy.co.uk',
# Minor finnish search engines
'haku','Ihmemaa',
# Minor french search engines
'aolfr','AOL (fr)', 'ctrouve','C\'est trouvé', 'francite','Francité', 'lbb', 'LBB', 'libertysurf', 'Libertysurf', 'free', 'Free.fr', 'clubinternet', 'Club-internet',
'toile', 'Toile du Québec',
'biglotron','<a href="http://www.biglotron.com" title="Search Engine Home Page">Biglotron</a>',
'mozbot','<a href="http://www.mozbot.fr" title="Search Engine Home Page">Mozbot</a>',
# Minor German search engines
'aolde','AOL (de)',
'fireball','Fireball', 'infoseek','Infoseek', 'webde','Web.de',
'abacho','Abacho', 't-online','T-Online',
'allesklar','allesklar.de', 'meinestadt','meinestadt.de',
'metaspinner','metaspinner',
'metacrawler_de','metacrawler.de',
'wwweasel','<a href="http://wwweasel.de" title="Search Engine Home Page">WWWeasel</a>',
'netluchs','<a href="http://www.netluchs.de" title="Search Engine Home Page">Netluchs</a>',
'schoenerbrausen','<a href="http://www.schoenerbrausen.de" title="Search Engine Home Page">Schoenerbrausen/</a>',
# Minor hungarian search engines
'heureka','Heureka', 'origo','Origo-Vizsla', 'lapkereso','Startlapkeresõ', 'goliat','Góliát', 'indexhu','Index', 'wahoo','Wahoo', 'webmania','webmania.hu', 'internetto','Internetto Keresõ',
'tango_hu','<a href="http://tango.hu" title="Search Engine Home Page">Tango</a>',
'keresolap_hu','<a href="http://keresolap.hu" title="Search Engine Home Page">Tango keresolap</a>',
'polymeta_hu','<a href="http://www.polymeta.hu" title="Search Engine Home Page">Polymeta</a>',
# Minor Indian search engines
'sify','<a href="http://search.sify.com" title="Search Engine Home Page">Sify</a>',
# Minor Italian search engines
'virgilio','<a href="http://www.virgilio.it" title="Search Engine Home Page">Virgilio</a>',
'arianna','<a href="http://arianna.libero.it" title="Search Engine Home Page">Arianna</a>',
'supereva','<a href="http://search.supereva.com" title="Search Engine Home Page">Supereva</a>',
'kataweb','<a href="http://www.kataweb.it/ricerca/" title="Search Engine Home Page">Kataweb</a>',
'aliceitmaster','<a href="http://www.alice.it" title="Search Engine Home Page">search.alice.it.master</a>',
'aliceit','<a href="http://www.alice.it" title="Search Engine Home Page">alice.it</a>',
'gotuneed','<a href="http://www.gotuneed.com" title="Search Engine Home Page">got u need</a>',
'godado','Godado.it',
'jumpy\.it','Jumpy.it',
'shinyseek\.it','Shinyseek.it',
'teecnoit','<a href="http://www.teecno.it" title="Teecno home page">Teecno</a>',
# Minor Japanese search engines
'askjp','<a href="http://www.ask.jp" title="Search Engine Home Page">Ask Japan</a>',
'sagool','<a href="http://sagool.jp" title="Sagool home page">Sagool</a>',
# Minor Norwegian search engines
'start','start.no', 'eniro','<a href="http://www.eniro.no" title="Search Engine Home Page">Eniro</a>',
# Minor polish search engines
'wp','<a href="http://szukaj.wp.pl" title="Wirtualna Polska home page">Wirtualna Polska</a>',
'onetpl','<a href="http://szukaj.onet.pl" title="Onet.pl home page">Onet.pl</a>',
'dodajpl','<a href="http://www.dodaj.pl" title="Dodaj.pl home page">Dodaj.pl</a>',
'gazetapl','<a href="http://szukaj.gazeta.pl" title="Gazeta.pl home page">Gazeta.pl</a>',
'gerypl','<a href="http://szukaj.gery.pl" title="Gery.pl home page">Gery.pl</a>',
'hogapl','<a href="http://www.hoga.pl" title="Hoga.pl home page">Hoga.pl</a>',
'netsprintpl','<a href="http://www.netsprint.pl" title="NetSprint.pl home page">NetSprint.pl</a>',
'interiapl','<a href="http://www.google.interia.pl" title="Interia.pl home page">Interia.pl</a>',
'katalogonetpl','<a href="http://katalog.onet.pl" title="Katalog.Onet.pl home page">Katalog.Onet.pl</a>',
'o2pl','<a href="http://szukaj2.o2.pl" title="o2.pl home page">o2.pl</a>',
'polskapl','<a href="http://szukaj.polska.pl" title="Polska home page">Polska</a>',
'szukaczpl','<a href="http://www.szukacz.pl" title="Szukacz home page">Szukacz</a>',
'wowpl','<a href="http://szukaj.wow.pl" title="Wow.pl home page">Wow.pl</a>',
# Minor russian search engines
'yandex', 'Yandex', 'aport', 'Aport', 'rambler', 'Rambler', 'turtle', 'Turtle', 'metabot', 'MetaBot',
# Minor Swedish search engines
'passagen','Evreka',
'enirose','<a href="http://www.eniro.se" title="Eniro Sverige home page">Eniro Sverige</a>',
# Minor Slovak search engines
'zoznam','<a href="http://www.zoznam.sk" title="Zoznam search engine home page">Zoznam</a>',
# Minor Portuguese search engines
'sapo','<a href="http://www.sapo.pt" title="Sapo search engine home page">Sapo</a>',
# Minor Swiss search engines
'searchch', 'search.ch', 'bluewin', 'search.bluewin.ch',
# Minor Croatian, Serbian, Macedonian, Bosnian and Herzegovinian search engines
'pogodak','<a href="http://www.pogodak.com" title="Pogodak search engine home page">Pogodak.com</a>',
# Generic search engines
'search',"$Message[215]"
);


# Sanity check.
# Enable this code and run perl search_engines.pm to check file entries are ok
#-----------------------------------------------------------------------------
#foreach my $key (@SearchEnginesSearchIDOrder_list1) {
#	if (! $SearchEnginesHashID{$key}) { error("Entry '$key' has been found in SearchEnginesSearchIDOrder_list1 with no value in SearchEnginesHashID");
#	foreach my $key2 (@SearchEnginesSearchIDOrder_list2) { if ($key2 eq $key) { error("$key is in 1 and 2\n"); } }
#	foreach my $key2 (@SearchEnginesSearchIDOrder_listgen) { if ($key2 eq $key) { error("$key is in 1 and gen\n"); } }
#} }
#foreach my $key (@SearchEnginesSearchIDOrder_list2) {
#	if (! $SearchEnginesHashID{$key}) { error("Entry '$key' has been found in SearchEnginesSearchIDOrder_list1 with no value in SearchEnginesHashID");
#	foreach my $key2 (@SearchEnginesSearchIDOrder_list1) { if ($key2 eq $key) { error("$key is in 2 and 1\n"); } }
#	foreach my $key2 (@SearchEnginesSearchIDOrder_listgen) { if ($key2 eq $key) { error("$key is in 2 and gen\n"); } }
#} }
#foreach my $key (@SearchEnginesSearchIDOrder_listgen) { if (! $SearchEnginesHashID{$key}) { error("Entry '$key' has been found in SearchEnginesSearchIDOrder_listgen with no value in SearchEnginesHashID"); } }
#foreach my $key (keys %NotSearchEnginesKeys) { if (! $SearchEnginesHashID{$key}) { error("Entry '$key' has been found in NotSearchEnginesKeys with no value in SearchEnginesHashID"); } }
#foreach my $key (keys %SearchEnginesKnownUrl) {
#	my $found=0;
#	foreach my $key2 (values %SearchEnginesHashID) {
#		if ($key eq $key2) { $found=1; last; }
#	}
#	if (! $found) { die "Entry '$key' has been found in SearchEnginesKnownUrl with no value in SearchEnginesHashID"; }
#}
#foreach my $key (keys %SearchEnginesHashLib) {
#	my $found=0;
#	foreach my $key2 (values %SearchEnginesHashID) {
#		if ($key eq $key2) { $found=1; last; }
#	}
#	if (! $found) { die "Entry '$key' has been found in SearchEnginesHashLib with no value in SearchEnginesHashID"; }
#}
#print @SearchEnginesSearchIDOrder_list1." ".@SearchEnginesSearchIDOrder_list2." ".@SearchEnginesSearchIDOrder_listgen;

1;
