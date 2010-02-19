# AWSTATS ROBOTS DATABASE
#-------------------------------------------------------
# If you want to add robots to extend AWStats database detection capabilities,
# you must add an entry in RobotsSearchIDOrder_listx and RobotsHashIDLib.
#-------------------------------------------------------
# $Revision: 1.59 $ - $Author: eldy $ - $Date: 2009/09/12 11:06:55 $
# Modified by makoto_hobbit　ホビット 2009.10.14

# 2005-08-19 Sean Carlos http://www.antezeta.com/awstats.html
#              added dipsie (not tested with real data).
#              added DomainsDB.net http://domainsdb.net/
#              added ia_archiver-web.archive.org (was inadvertently grouped with Alexa traffic)
#              added Nutch (used by looksmart (furl?))
#              added rssImagesBot
#              added Sqworm
#              added t\-h\-u\-n\-d\-e\-r\-s\-t\-o\-n\-e
#              added w3c css-validator
#              added documentation link to bot home pages for above and selected major bots.
#                    In the case of international bots, choose .com page.
#                    Included tool tip (html "title").
#                    To do: parameterize to match both AWStats language and tooltips settings.
#                    To do: add html links for all bots based on current documentation in source
#                           files referenced below.
#              changed '\wbot[\/\-]', to '\wbot[\/\-]' (removed comma)
#              made minor grammar corrections to notes below
# 2005-08-24	added YahooSeeker-Testing
#              	added w3c-checklink
#              	updated url for ask.com
# 2005-08-24   	added Girafabot http://www.girafa.com/
# 2005-08-30   	added PluckFeedCrawler http://www.pluck.com/
#		added Gaisbot/3.0 (robot05@gais.cs.ccu.edu.tw; )
#		dded geniebot (wgao@genieknows.com)
#		added BecomeBot link http://www.become.com/site_owners.html
#		added topicblogs http://www.topicblogs.com/
#		added Powermarks; seen used by referrer spam
#		added YahooSeeker
#		added NG/2. http://www.exabot.com/
# 2005-09-15	added link for Walhello appie
#		added bender focused_crawler
#		updated YahooSeeker description (blog crawler)
# 2005-09-16	added link for http://linkchecker.sourceforge.net
# 		added ConveraCrawler/0.9d ( http://www.authoritativeweb.com/crawl)
#		added Blogslive  info@blogslive.com intelliseek.com
#		added BlogPulse (ISSpider-3.0) intelliseek.com
# 2005-09-26	added Feedfetcher-Google (http://www.google.com/feedfetcher.html)
#		added EverbeeCrawler
#		added Yahoo-Blogs http://help.yahoo.com/help/us/ysearch/crawling/crawling-02.html
#		added link for Bloglines http://www.bloglines.com
# 2005-10-19	fixed Feedfetcher-Google (http://www.google.com/feedfetcher.html)
# 		added Blogshares Spiders (Synchronized V1.5.1)
#		added yacy
# 2005-11-21	added Argus www.simpy.com
#		added BlogsSay :: RSS Search Crawler (http://www.blogssay.com/)
#		added MJ12bot http://majestic12.co.uk/bot.php
#		added OpenTaggerBot (http://www.opentagger.com/opentaggerbot.htm)
#		added OutfoxBot/0.3 (For internet experiments; outfox.agent@gmail.com)
#		added RufusBot Rufus Web Miner http://64.124.122.252.webaroo.com/feedback.html
#		added Seekbot (http://www.seekbot.net/bot.html)
#		added Yahoo-MMCrawler/3.x (mms-mmcrawler-support@yahoo-inc.com)
#               added link for BaiDuSpider
#		added link for Blogshares Spider
#		added link for StackRambler http://www.rambler.ru/doc/faq.shtml
#		added link for WISENutbot
#		added link for ZyBorg/1.0 (wn-14.zyborg@looksmart.net; http://www.WISEnutbot.com.  Moved location to above wisenut to avoid classification as wisenut
# 2005-12-15
#		added FAST Enteprise Crawler/6 (www dot fastsearch dot com). Note spelling Enteprise not Enterprise.
#		added findlinks http://wortschatz.uni-leipzig.de/findlinks/
#		added IBM Almaden Research Center WebFountain™ http://www.almaden.ibm.com/cs/crawler [hc3]
#		added INFOMINE/8.0 VLCrawler (http://infomine.ucr.edu/useragents)
#		added lmspider (lmspider@scansoft.com) http://www.nuance.com/
#		added noxtrumbot http://www.noxtrum.com/
#		added SandCrawler (Microsoft)
#		added SBIder http://www.sitesell.com/sbider.html
#		added SeznamBot http://fulltext.seznam.cz/
#		added sohu-search http://corp.sohu.com/ (looked for //robots.txt not /robots.txt)
#		added the ruffle SemanticWeb crawler v0.5 - http://www.unreach.net
#		added WebVulnCrawl/1.0 libwww-perl/5.803 (looked for //robots.txt not /robots.txt)
#		added Yahoo! Japan keyoshid http://www.yahoo.co.jp/
#		added Y!J http://help.yahoo.co.jp/help/jp/search/indexing/indexing-15.html
#		added link for GigaBot
#		added link for MagpieRSS
#		added link for MSIECrawler
# 2005-12-21
#		added aipbot http://www.aipbot.com aipbot@aipbot.com [matthys70 users.sourceforge.net]
#		added Everest-Vulcan Inc./0.1 (R&D project; http://everest.vulcan.com/crawlerhelp)
#		added Fast-Search-Engine http://www.fast-search-engine.com/ [matthys70  users.sourceforge.net]
#		added g2Crawler (nobody@airmail.net) http://crawler.instantnetworks.net/
#		added Jakarta commons-httpclient http://jakarta.apache.org/commons/httpclient/ (hit robots.txt).  May be used as robot or browser - a site may want to remove this entry.
#		added OmniExplorer_Bot http://www.omni-explorer.com/ [matthys70 users.sourceforge.net]
#		added USTC-Semantic-Group ai.ustc.edu.cn/mas/en/research/index.php ?
# 2005-12-22
#		added EARTHCOM.info www.earthcom.info
#		added HTTrack off-line browser 'httrack','HTTrack', http://www.httrack.com/ [Moizes Gabor]
#		added KummHttp http://www.psychedelix.com/cgi-bin/csv2html.pl?data=allagents.csv&template=detail.html&match=\bid_g_l_301105_2\b [Moizes Gabor]
# 2006-01-01
#		added Dulance http://www.dulance.com/bot.jsp
#		added MojeekBot http://www.mojeek.com/bot.html
#		added nicebot http://www.egghelp.org/setup.htm ?
#		added Snappy http://www.urltrends.com/faq.php
#		added sohu agent
#		added VORTEX http://marty.anstey.ca/robots/vortex/ [matthys70 users.sourceforge.net]
#		added zspider http://feedback.redkolibri.com/
# 2006-01-13
#		added boitho.com-dc http://www.boitho.com/dcbot.html
#		added IRLbot http://irl.cs.tamu.edu/crawler
#		added virus_detector virus_harvester@securecomputing.com
#		added Wavefire http://www.wavefire.com; info@wavefire.com
#		added WebFilter Robot
# 2006-01-24
#		added Shim-Crawler http://www.logos.ic.i.u-tokyo.ac.jp/crawler/; crawl@logos.ic.i.u-tokyo.ac.jp
#		added Exabot exabot.com
#		added LetsCrawl.com http://letscrawl.com
#		added ichiro http://help.goo.ne.jp/door/crawlerE.html
# 2006-01-27    additional 22 robots from a list provided by Moizes Gabor
#		added ALeadSoftbot	http://www.aleadsoft.com/bot.htm
#		added CipinetBot	http://www.cipinet.com/bot.html
#		added Cuasarbot	http://www.cuasar.com/
#		added Dumbot	http://www.dumbfind.com/
#		added Extreme_Picture_Finder	http://www.exisoftware.com/
#		added Fooky.com/ScorpionBot/ScoutOut	http://www.fooky.com/scorpionbots
#		added IlTrovatore-Setaccio	http://www.iltrovatore.it/aiuto/motore_di_ricerca.html	bot@iltrovatore.it
#		added InsurancoBot	http://www.fastspywareremoval.com/
#		added InternetArchive	http://lucene.apache.org/nutch/bot.html 	nutch-agent@lucene.apache.org
#		added KazoomBot	http://www.kazoom.ca/bot.html	kazoombot@kazoom.ca
#		added Kurzor	http://www.easymail.hu/	cursor@easymail.hu
#		added NutchCVS	http://lucene.apache.org/nutch/bot.html	nutch-agent@lucene.apache.org
#		added NutchOSU-VLIB	http://lucene.apache.org/nutch/bot.html	nutch-agent@lucene.apache.org
#		added Orbiter	http://www.dailyorbit.com/bot.htm
#		added PHP_version_tracker	http://www.nexen.net/phpversion/bot.php
#		added SuperBot	http://www.sparkleware.com/superbot/
#		added SynooBot	http://www.synoo.de/bot.html	webmaster@synoo.com
#		added TestBot	http://www.agbrain.com/
#		added TutorGigBot	http://www.tutorgig.info/
#		added WebIndexer	mailto://webindexerv1@yahoo.com
#		added WebMiner	http://64.124.122.252/feedback.html
# 2006-02-01
#		added heritrix https://sourceforge.net/forum/message.php?msg_id=3550202
#		added Zeus Webster Pro https://sourceforge.net/forum/message.php?msg_id=3141164
#               additional robots from a list provided by Moizes Gabor [ mojzi -a-t- free mail hu ]
#		added Candlelight_Favorites_Inspector
#		added DomainChecker
#		added EasyDL
#		added FavOrg
#		added Favorites_Sweeper
#		added Html_Link_Validator
#		added Internet_Ninja
#		added JRTwine_Software_Check_Favorites_Utility
#		fixed Microsoft_URL_Control
#		added miniRank
#		added Missigua_Locator
#		added NPBot
#		added Ocelli
#		added Onet.pl_SA
#		added proodleBot
#		added SearchGuild_DMOZ_Experiment
#		added Susie
#		added Website_Monitoring_Bot
#		added Xenu_Link_Sleuth
# 2006-05-15
#		added ASPseek http://www.aspseek.org/
#		added AdamM Bot http://home.blic.net/adamm/
#		added archive.org_bot http://crawls.archive.org/collections/bncf/crawl.html
#		added arianna.libero.it (Italian Portal/search engine)
#		added Biz360 spider http://www.biz360.com
#		added BlogBridge Service http://www.blogbridge.com/
#		added BlogSearch http://www.icerocket.com/
#		added libcrawl
#		added edgeio-relanshanbottriever http://www.edgeio.com
#		added FeedFlow http://feedflow.com/about
#		added Biblioteca Nazionale Centrale di Firenze (Italian National Archive) http://www.bncf.firenze.sbn.it/raccolta.txt
#		added Java catchall - used by many spam bots
#		added lanshanbot http://www.psychedelix.com/cgi-bin/csv2html.pl?data=allagents.csv&amp;template=detail.html&amp;match=%5Cbid_g_l_140406_1%5Cb
#		added msnbot-media http://search.msn.com/msnbot.htm
#		added MT::Telegraph::Agent
#		added Netluchs http://www.netluchs.de/ (German SE bot)
#		added oBot http://www.webmasterworld.com/forum11/1616.htm
#		added Onfolio http://www.onfolio.com/  (IE Toolbar plugin) - hit rss feeds.
#		added ping.blo.gs http://blo.gs/ping.php blog bot
#		added Sphere Scout http://www.sphere.com/
#		added sproose crawler http://www.sproose.com/bot.html
#		added SyndicAPI http://syndicapi.com/bot.html
#		added Yahoo! Mindset http://mindset.research.yahoo.com/
#		added msrabot
#		added Vagabondo & Vagabondo-WAP http://www.wise-guys.nl/Contact/index.php?botselected=webagents&amp;lang=uk
#		fixed Missigua Locator detection (Missigua_Locator -> Missigua Locator)
#		changed echo to echo! to avoid conflict with the bonecho (Firefox 2.0) browser.
#			This requires you to reprocess historic logs if you want EchO! to be recognized for older reports.
# 2006-05-17
#		added Alpha Search Agent # 62.152.125.60 Eurologon Srl
#		added Krugle http://www.krugle.com/crawler/info.html the search engine for developers
#		added Octora Beta Bot http://www.octora.com/ # Blog and Rss Search Engine
#		added UbiCrawler http://law.dsi.unimi.it/ubicrawler/
#		added Yahoo! Slurp China http://misc.yahoo.com.cn/help.html
#			You must reprocess old logs for the Yahoo! Slurp China bot to be detected in old reports
# 2006-05-20
#		added 1-More Scanner http://www.myzips.com/software/1-More-Scanner.phtml
#		added Accoona-AI-Agent http://www.accoona.com/
#		added ActiveBookmark http://www.libmaster.com/active_bookmark.php
#		added BIGLOTRON http://www.biglotron.com/robot.html
#		added Bookmark-Manager http://bkm.sourceforge.net/
#		added cbn00glebot
#		added Cerberian Drtrs http://www.pgts.com.au/cgi-bin/psql?robot_info=25240
#		added CFNetwork http://www.cocoadev.com/index.pl?CFNetwork
#		added CheckWeb link validator http://p.duby.free.fr/chkweb.htm
#		added Computer and Automation Research Institute Crawler http://www.ilab.sztaki.hu/~stamas/publications/p184-benczur.html
#		added ConveraCrawler http://www.authoritativeweb.com/crawl/
#		added ConveraMultiMediaCrawler http://www.authoritativeweb.com/crawl/
#		added CSE HTML Validator Lite Online http://online.htmlvalidator.com/php/onlinevallite.php
#		added Cursor http://adcenter.hu/docs/en/bot.html
#		added Custo http://www.netwu.com/custo/
#		added DataFountains/DMOZ Downloader http://infomine.ucr.edu/
#		added Deepindex http://www.deepindex.net/faq.php
#		added DNSGroup http://www.dnsgroup.com/
#		added DoCoMo http://www.nttdocomo.co.jp/
#		added dumm.de-Bot http://www.dumm.de/
#		added ETS v http://www.freetranslation.com/help/
#		added eventax http://www.eventax.de/
#		added FAST Enterprise Crawler * crawleradmin.t-info@telekom.de http://www.telekom.de/
#		added FAST Enterprise Crawler http://www.fast.no/
#		added FAST Enterprise Crawler * T-Info_BI_cluster crawleradmin.t-info@telekom.de http://www.telekom.de/
#		added FeedValidator http://feedvalidator.org/
#		added FilmkameraBot http://www.filmkamera.at/bot.html
#		added Findexa Crawler http://www.findexa.no/gulesider/article26548.ece
#		added Global Fetch http://www.wesonet.com/
#		added GOFORITBOT http://www.goforit.com/about/
#		added GoForIt.com http://www.goforit.com/about/
#		added GPU p2p crawler http://gpu.sourceforge.net/search_engine.php
#		added HooWWWer http://cosco.hiit.fi/search/hoowwwer/
#		added HPPrint
#		added HTMLParser http://htmlparser.sourceforge.net/
#		added Hundesuche.com-Bot http://www.hundesuche.com/
#		added InfoBot http://www.infobot.org/
#		added InfociousBot http://corp.infocious.com/tech_crawler.php
#		added InternetSupervision http://internetsupervision.com/
#		added isearch2006 http://www.yahoo.com.cn/
#		added IUPUI_Research_Bot http://spamhuntress.com/2005/04/25/a-mail-harvester-visits/
#		added KalamBot http://64.124.122.251/feedback.html
#		added kamano.de NewsFeedVerzeichnis http://www.kamano.de/
#		added Kevin http://dznet.com/kevin/
#		added KnowItAll http://www.cs.washington.edu/research/knowitall/
#		added Knowledge.com http://www.knowledge.com/
#		added Kouaa Krawler http://www.kouaa.com/
#		added ksibot http://ego.ms.mff.cuni.cz/
#		added Link Valet Online http://www.htmlhelp.com/tools/valet/
#		added lwp-request http://search.cpan.org/~gaas/libwww-perl-5.69/bin/lwp-request
#		added lwp-trivial http://search.cpan.org/src/GAAS/libwww-perl-5.805/lib/LWP/Simple.pm
#		added MapoftheInternet.com http://MapoftheInternet.com/
#		added Matrix S.p.A. - FAST Enterprise Crawler http://tin.virgilio.it/
#		added Megite http://www.megite.com/
#		added Metaspinner http://index.meta-spinner.de/
#		added Mini-reptile
#		added Misterbot http://www.misterbot.fr/
#		added Miva http://www.miva.com/
#		added Mizzu Labs http://www.psychedelix.com/cgi-bin/csv2html.pl?data=allagents.csv&amp;template=detail.html&amp;match=\bid_m_141105_2\b
#		added MSRBOT http://research.microsoft.com/research/sv/msrbot/
#		added MS SharePoint Portal Server - MS Search 4.0 Robot http://support.microsoft.com/default.aspx?scid=kb;en-us;284022
#		added Mydoyouhike http://www.doyouhike.net/my
#		added NASA Search http://www.psychedelix.com/cgi-bin/csv2html.pl?data=allagents.csv&template=detail.html&match=\bid_n_s_140506_2\b
#		added NetSprint http://www.netsprint.pl/serwis/
#		added NimbleCrawler http://www.healthline.com/
#		added OpenWebSpider http://www.openwebspider.org/
#		added Oracle Ultra Search http://www.oracle.com/technology/products/ultrasearch/index.html
#		added OSSProxy http://www.marketscore.com/FAQ.Aspx
#		added passwordmaker.org http://passwordmaker.org/
#		added PEAR HTTP Request class http://pear.php.net/
#		added PEERbot http://www.peerbot.com/
#		added PHP version tracker http://www.nexen.net/phpversion/bot.php
#		added PictureOfInternet http://malfunction.org/poi/
#		added plinki http://www.plinki.com/
#		added Port Huron Labs http://www.psychedelix.com/cgi-bin/csv2html.pl?data=allagents.csv&amp;template=detail.html&amp;match=\bid_n_s_1133\b
#		added PostFavorites http://www.psychedelix.com/cgi-bin/csv2html.pl?data=allagents.csv&amp;template=detail.html&amp;match=\bid_n_s_1135\b
#		added ProjectWF-java-test-crawler
#		added PyQuery http://sourceforge.net/projects/pyquery/
#		added Schizozilla http://spamhuntress.com/2005/03/18/gizmo/
#		added Scumbot
#		added Sensis Web Crawler http://www.sensis.com.au/
#		added snap.com beta crawler http://www.snap.com/
#		added Steeler http://www.tkl.iis.u-tokyo.ac.jp/~crawler/
#		added STEROID  Download http://faqs.org.ru/progr/pascal/delphi_internet2.htm
#		added Suchfin-Bot http://www.suchfin.de/
#		added Sunrise http://www.sunrisexp.com/
#		added Tagyu Agent http://www.tagyu.com/
#		added Tcl http client package http://www.tcl.tk/man/tcl8.4/TclCmd/http.htm
#		added TeragramCrawlerSURF http://www.teragram.com/
#		added Test Crawler http://netp.ath.cx/
#		added UnChaos Bot Hybrid Web Search Engine http://www.unchaos.com/
#		added unido-bot http://www.unchina.org/unido/unido/our_projects/3_3.html
#		added UniversalFeedParser http://feedparser.org/ (seen from md301000.inktomisearch.com)
#		added updated http://www.updated.com/
#		added Vermut http://vermut.aol.com
#		added versus crawler from eda.baykan@epfl.ch http://www.epfl.ch/Eindex.html
#		added Vespa Crawler (Yahoo Norway?) http://www.psychedelix.com/cgi-bin/csv2html.pl?data=allagents.csv&template=detail.html&match=%5Cbid_t_z_030406_1%5Cb
#		added VSE http://www.vivisimo.com/
#		added webcrawl.net http://www.webcrawl.net/
#		added Web Downloader http://www.krasu.ru/soft/chuchelo/
#		added Webdup http://www.webdup.com/en/index.html
#		added Wells Search http://www.psychedelix.com/cgi-bin/csv2html.pl?data=allagents.csv&amp;template=detail.html&amp;match=\bid_t_z_1484\b
#		added WordPress http://wordpress.org/
#		added wume crawler http://wume.cse.lehigh.edu/~xiq204/crawler/
#		added Xenu's Link Sleuth (with ')
#		added xirq http://www.xirq.com/
#		added yoogliFetchAgent http://www.yoogli.com/
#		added Z-Add Link Checker http://w3.z-add.co.uk/linkcheck/
#		-- fix - some robots were reported with _ where _ should have been a space.
#		changed Xenu Link Sleuth
#		changed microsoft[_+ ]url[_+ ]control -> microsoft_url_control
#		changed favorites_sweeper -> favorites_sweeper
#		-- updates
#		updated AskJeeves to Ask

# to do  MS Search 4.0 Robot

#package AWSROB;


# Robots list was found at http://www.robotstxt.org/wc/active/all.txt
# Other robots can be found at http://www.jafsoft.com/searchengines/webbots.html
# Rem: To avoid bad detection, some robot's ids were removed from this list:
#      - Robots with ID of 3 letters only
#      - Robots called 'webs' and 'tcl'
# Rem: directhit changed into direct_hit (its real id)
# Rem: calif changed into calif[^r] to avoid confusion between Tiscalifreenet browser
# Rem: fish changed into [^a]fish to avoid confusion between Madsafish browser
# Rem: roadrunner changed into road_runner
# Rem: lycos changed to lycos_ to avoid confusion with lycos-online browser
# Rem: voyager changed into ^voyager\/ to avoid to exclude voyager and amigavoyager browser

# RobotsSearchIDOrder
# It contains all matching criteria to search for in log fields. This list is
# used to know in which order to search Robot IDs.
# Most frequent ones are in list1, used when LevelForRobotsDetection is 1 or more
# Minor robots are in list2, used when LevelForRobotsDetection is 2 or more
# Note: Robots IDs are in lower case, '_', ' ' and '+' are changed into '[_+ ]' and are quoted.
#-------------------------------------------------------
@RobotsSearchIDOrder_list1 = (
# Japanese Robots+alpha
# Added by Ryu 2007.01.06
'another_html\-lint',
'adsenserbot',
'adsbot\-google',
'amfibibot',
'baiduimagespider',
'baidumobaider',
'becomejpbot',
'bibliotheca',
'bizpal\srss\saggregator',
'bookmark\srenewal\scheck\sagent',
'camelstampede',
'cazoodlebot',
'charlotte',
'coast\swebmaster',
'cococ',
'comaneci_bot',
'crooz',
'daumoa',
'dealgates\sbot',
'depspid\/',
'diffbrowser\/',
'dotbot',
'ec_favorite',
'empas_robot',
'e\-societyrobot',
'everyfeed-spider',
'faedit\/',
'fast\smetaweb\scrawler',
'fastladder\sfeedfetcher',
'feed24\.com',
'feedbringer',
'feedchecker',
'feed\sparser',
'feedpath',
'feedshow',
'fly\/',
'freshreader',
'fsbot',
'g10\sreader',
'gingercrawler',
'goorssreader',
'grub\-client',
'gsa\-crawler',
'hatena',
'headline\-reader',
'hobbit\sbbtest\-net',
'html\sget',
'hyperestraier',
'hyperrobot',
'icc\-crawler',
'ilissurf',
'indexpert',
'internal\sdummy\sconnection',
'jetbot',
'kb\.rmail',
'keywalkerbot',
'klsh\-pageget\/',
'kotoha\.co\.jp',
'libghttp\/',
'livedoorcheckers',
'livedoor\sfeedfetcher',
'livedoor\sscreenshot\/',
'livedoor\shttpclient\/',
'maldive\scrawler',
'masagool',
'mfcrawler',
'mogimogi\/',
'metalogger',
'mlbot',
'mqbot',
'msr\-isrccrawler',
'multicrawler',
'naver',
'niyonizer',
'ndl\-japan\-research\-robot',
'netresearchserver',
'nettracker',
'netvibes',
'newsalloy',
'^obot',
'onetszukaj',
'openbot',
'oracle\ssecure\senterprise\ssearch',
'outfoxbot',
'page_verifier',
'pear\shttp_request\sclass',
'paipo\-bot',
'pathtraq',
'pingdom\sgigrib',
'pipeliner',
'playon\srss\sreader\/',
'pockey\-gethtml',
'protopage\/',
'research\-spider',
'search\-hp_bot',
'seo\.cug\.net\slink\schecker',
'shopwiki\/',
'smart\.apnoti\.com',
'snapbot',
'snoopy\sv',
'sogou\sweb\sspider',
'sonar\/', #Added by toshi 2006.04.09
'sonarplus\/',
'spamrobot@126\.com',
'sproose\/',
'stackrambler',
'strategic\sboard\sbot',
'statbot@gmail\.com',
'technoratisnoop',
'techrigybot',
'tencenttraveler', # Must be before msiecrawler
'trackback\/',
'umn\/',
'useragent',
'w3crobot',
'wadaino\.jp\-crawler',
'webalta',
'webauto',
'webaroobot',
'webdigity\swhois\sservice',
'website\sexplorer',
'wish\-la',
'wish\-project',
'wiwi',
'wwwster\/',
'yahoo!-adcrawler',
'yeti\/',
'zao\-crawler',
'zibber',
'^-$',
# Common robots (In robot file)
'appie',
'architext',
'jeeves\/',
'bjaaland',
'contentmatch',
'ferret',
'googlebot',
'google\-sitemaps',
'gulliver',
'virus\_detector',		# Must be before harvest
'harvest',
'htdig',
'linkwalker',
'lilina',
'lycos_',
'moget',
'muscatferret',
'myweb',
'nomad',
'scooter',
'slurp',
'^voyager\/',
'weblayers',
# Common robots (Not in robot file)
'antibot',
'bruinbot',
'digout4u',
'echo!',
'fast\-webcrawler',
'ia_archiver\-web\.archive\.org', # Must be before ia_archiver to avoid confusion with alexa
'ia_archiver',
'jennybot',
'mercator',
'netcraft',
'msnbot\-media',
'msnbot',
'petersnews',
'relevantnoise\.com',
'unlost_web_crawler',
'voila',
'webbase',
'webcollage',
'cfetch',
'zyborg',	# Must be before wisenut
'wisenutbot'
);
@RobotsSearchIDOrder_list2 = (
# Less common robots (In robot file)
'[^a]fish',
'abcdatos',
'acme\.spider',
'ahoythehomepagefinder',
'alkaline',
'anthill',
'arachnophilia',
'arale',
'araneo',
'aretha',
'ariadne',
'powermarks',
'arks',
'aspider',
'atn\.txt',
'atomz',
'auresys',
'backrub',
'bbot',
'bigbrother',
'blackwidow',
'blindekuh',
'bloodhound',
'borg\-bot',
'brightnet',
'bspider',
'cactvschemistryspider',
'calif[^r]',
'cassandra',
'cgireader',
'checkbot',
'christcrawler',
'churl',
'cienciaficcion',
'collective',
'combine',
'conceptbot',
'coolbot',
'core',
'cosmos',
'cruiser',
'cusco',
'cyberspyder',
'desertrealm',
'deweb',
'dienstspider',
'digger',
'diibot',
'direct_hit',
'dnabot',
'download_express',
'dragonbot',
'dwcp',
'e\-collector',
'ebiness',
'elfinbot',
'emacs',
'emcspider',
'esther',
'evliyacelebi',
'fastcrawler',
'feedcrawl',
'fdse',
'felix',
'fetchrover',
'fido',
'finnish',
'fireball',
'fouineur',
'francoroute',
'freecrawl',
'funnelweb',
'gama',
'gazz',
'gcreep',
'getbot',
'geturl',
'golem',
'gougou',
'grapnel',
'griffon',
'gromit',
'gulperbot',
'hambot',
'havindex',
'hometown',
'htmlgobble',
'hyperdecontextualizer',
'iajabot',
'iaskspider',
'hl_ftien_spider',
'sogou',
'iconoclast',
'ilse',
'imagelock',
'incywincy',
'informant',
'infoseek',
'infoseeksidewinder',
'infospider',
'inspectorwww',
'intelliagent',
'irobot',
'iron33',
'israelisearch',
'javabee',
'jbot',
'jcrawler',
'jobo',
'jobot',
'joebot',
'jubii',
'jumpstation',
'kapsi',
'katipo',
'kilroy',
'ko_yappo_robot',
'kummhttp',
'labelgrabber\.txt',
'larbin',
'legs',
'linkidator',
'linkscan',
'lockon',
'logo_gif',
'macworm',
'magpie',
'marvin',
'mattie',
'mediafox',
'merzscope',
'meshexplorer',
'mindcrawler',
'mnogosearch',
'momspider',
'monster',
'motor',
'muncher',
'mwdsearch',
'ndspider',
'nederland\.zoek',
'netcarta',
'netmechanic',
'netscoop',
'newscan\-online',
'nhse',
'northstar',
'nzexplorer',
'objectssearch',
'occam',
'octopus',
'openfind',
'orb_search',
'packrat',
'pageboy',
'parasite',
'patric',
'pegasus',
'perignator',
'perlcrawler',
'phantom',
'phpdig',
'piltdownman',
'pimptrain',
'pioneer',
'pitkow',
'pjspider',
'plumtreewebaccessor',
'poppi',
'portalb',
'psbot',
'python',
'raven',
'rbse',
'resumerobot',
'rhcs',
'road_runner',
'robbie',
'robi',
'robocrawl',
'robofox',
'robozilla',
'roverbot',
'rules',
'safetynetrobot',
'search\-info',
'search_au',
'searchprocess',
'senrigan',
'sgscout',
'shaggy',
'shaihulud',
'sift',
'simbot',
'site\-valet',
'sitetech',
'skymob',
'slcrawler',
'smartspider',
'snooper',
'solbot',
'speedy',
'spider_monkey',
'spiderbot',
'spiderline',
'spiderman',
'spiderview',
'spry',
'sqworm',
'ssearcher',
'suke',
'sunrise',
'suntek',
'sven',
'tach_bw',
'tagyu_agent',
'tailrank',
'tarantula',
'tarspider',
'techbot',
'templeton',
'titan',
'titin',
'tkwww',
'tlspider',
'ucsd',
'udmsearch',
'universalfeedparser',
'urlck',
'valkyrie',
'verticrawl',
'victoria',
'visionsearch',
'voidbot',
'vwbot',
'w3index',
'w3m2',
'wallpaper',
'wanderer',
'wapspIRLider',
'webbandit',
'webcatcher',
'webcopy',
'webfetcher',
'webfoot',
'webinator',
'weblinker',
'webmirror',
'webmoose',
'webquest',
'webreader',
'webreaper',
'websnarf',
'webspider',
'webvac',
'webwalk',
'webwalker',
'webwatch',
'whatuseek',
'whowhere',
'wired\-digital',
'wmir',
'wolp',
'wombat',
'wordpress',
'worm',
'woozweb',
'wwwc',
'wz101',
'xget',
# Other robots reported by users
'1\-more_scanner',
'accoona\-ai\-agent',
'activebookmark',
'adamm_bot',
'almaden',
'aipbot',
'aleadsoftbot',
'alpha_search_agent',
'allrati',
'aport',
'archive\.org_bot',
'argus', 		# Must be before nutch
'arianna\.libero\.it',
'aspseek',
'asterias',
'awbot',
'baiduspider',
'becomebot',
'bender',
'betabot',
'biglotron',
'bittorrent_bot',
'biz360\sspider',
'blogbridge\sservice',
'bloglines',
'blogpulse',
'blogsearch',
'blogshares',
'blogslive',
'blogssay',
'bncf\.firenze\.sbn\.it\/raccolta\.txt',
'bobby',
'boitho\.com\-dc',
'bookmark\-manager',
'boris',
'bumblebee',
'candlelight\_favorites\_inspector',
'cbn00glebot',
'cerberian\sdrtrs',
'cfnetwork',
'cipinetbot',
'checkweb_link_validator',
'commons\-httpclient',
'computer_and_automation_research_institute_crawler',
'converamultimediacrawler',
'converacrawler',
'cscrawler',
'cse_html_validator_lite_online',
'cuasarbot',
'cursor',
'custo',
'datafountains\/dmoz_downloader',
'daviesbot',
'daypopbot',
'deepindex',
'dipsie\.bot',
'dnsgroup',
#'docomo',
'domainchecker',
'domainsdb\.net',
'dulance',
'dumbot',
'dumm\.de\-bot',
'earthcom\.info',
'easydl',
'edgeio\-retriever',
'ets_v',
'exactseek',
'extreme\_picture\_finder',
'eventax',
'everbeecrawler',
'everest\-vulcan',
'ezresult',
'enteprise',
'facebook',
'fast_enterprise_crawler.*crawleradmin\.t\-info@telekom\.de',
'fast_enterprise_crawler.*t\-info_bi_cluster_crawleradmin\.t\-info@telekom\.de',
'matrix_s\.p\.a\._\-_fast_enterprise_crawler', # must come before fast enterprise crawler
'fast\senterprise\scrawler',
'fast\-search\-engine',
'favicon',
'favorg',
'favorites_sweeper',
'feedburner',
'feedfetcher\-google',
'feedflow',
'feedster',
'feedsky',
'feedvalidator',
'filmkamerabot',
'findlinks',
'findexa_crawler',
'fooky\.com\/ScorpionBot',
'g2crawler',
'gaisbot',
'geniebot',
'gigabot',
'girafabot',
'global_fetch',
'gnodspider',
'goforit\.com',
'goforitbot',
'grub',
'gpu_p2p_crawler',
'henrythemiragorobot',
'heritrix',
'holmes',
'hoowwwer',
'hpprint',
'htmlparser',
'html\_link\_validator',
'httrack',
'hundesuche\.com\-bot',
'ichiro',
'iltrovatore\-setaccio',
'infobot',
'infociousbot',
'infomine',
'insurancobot',
'internet\_ninja',
'internetarchive',
'internetseer',
'internetsupervision',
'irlbot',
'isearch2006',
'iupui_research_bot',
'jrtwine\_software\_check\_favorites\_utility',
'justview',
'kalambot',
'kamano\.de_newsfeedverzeichnis',
'kazoombot',
'kevin',
'keyoshid', # Must come before Y!J
'kinjabot',
'kinja\-imagebot',
'knowitall',
'knowledge\.com',
'kouaa_krawler',
'krugle',
'ksibot',
'kurzor',
'lanshanbot',
'letscrawl\.com',
'libcrawl',
'linkbot',
'link_valet_online',
'metager\-linkchecker',	# Must be before linkchecker
'linkchecker',
'livejournal\.com',
'lmspider',
'lwp\-request',
'lwp\-trivial',
'magpierss',
'mail\.ru',
'mapoftheinternet\.com',
'mediapartners\-google',
'megite',
'metaspinner',
'microsoft\surl\scontrol',
'mini\-reptile',
'minirank',
'missigua_locator',
'misterbot',
'miva',
'mizzu_labs',
'mj12bot',
'mojeekbot',
'msiecrawler',
'ms\ssearch\s4\.0\srobot',
'msrabot',
'msrbot',
'mt::telegraph::agent',
'nagios',
'nasa_search',
'mydoyouhike',
'netluchs',
'netsprint',
'newsgatoronline',
'nicebot',
'nimblecrawler',
'noxtrumbot',
'npbot',
'nutchcvs',
'nutchosu\-vlib',
'nutch',  # Must come after other nutch versions
'ocelli',
'octora_beta_bot',
'omniexplorer\_bot',
'onet\.pl\_sa',
'onfolio',
'opentaggerbot',
'openwebspider',
'oracle_ultra_search',
'orbiter',
'yodaobot',
'qihoobot',
'passwordmaker\.org',
'pear_http_request_class',
'peerbot',
'perman',
'php\sversion\stracker',
'pictureofinternet',
'ping\.blo\.gs',
'plinki',
'pluckfeedcrawler',
'pogodak',
'pompos',
'popdexter',
'port_huron_labs',
'postfavorites',
'projectwf\-java\-test\-crawler',
'proodlebot',
'pyquery',
'rambler',
'redalert',
'rojo',
'rssimagesbot',
'ruffle',
'rufusbot',
'sandcrawler',
'sbider',
'schizozilla',
'scumbot',
'searchguild\_dmoz\_experiment',
'seekbot',
'sensis\sweb\scrawler',
'seznambot',
'shim\-crawler',
'shoutcast',
'slysearch',
'snap\.com_beta_crawler',
'sohu\-search',
'sohu', # "sohu agent"
'snappy',
'sphere\sscout',
'spip',
'sproose_crawler',
'steeler',
'steroid__download',
'suchfin\-bot',
'superbot',
'surveybot',
'susie',
'syndic8',
'syndicapi',
'synoobot',
'tcl_http_client_package',
'technoratibot',
'teragramcrawlersurf',
'test\scrawler',
'testbot',
't\-h\-u\-n\-d\-e\-r\-s\-t\-o\-n\-e',
'topicblogs',
'turnitinbot',
'turtlescanner',		# Must be before turtle
'turtle',
'tutorgigbot',
'twiceler',
'ubicrawler',
'ultraseek',
'unchaos_bot_hybrid_web_search_engine',
'unido\-bot',
'updated',
'ustc\-semantic\-group',
'vagabondo\-wap',
'vagabondo',
'vermut',
'versus_crawler_from_eda\.baykan@epfl\.ch',
'vespa_crawler',
'vortex',
'vse\/',
'w3c\-checklink',
'w3c\_css\_validator\_jfouffa',
'w3c_validator',
'wavefire',
'webclipping\.com',
'webcompass',
'webcrawl\.net',
'web_downloader',
'webdup',
'webfilter',
'webindexer',
'webminer',
'website\_monitoring\_bot',
'webvulncrawl',
'wells_search',
'wonderer',
'wume_crawler',
'wwweasel',
'xenu\'s_link_sleuth',
'xenu\slink\ssleuth',
'xirq',
'y!j', # Must come after keyoshid Y!J
'yacy',
'yahoo\-blogs',
'yahoo\-verticalcrawler',
'yahoofeedseeker',
'yahooseeker\-testing',
'yahooseeker',
'yahoo\-mmcrawler',
'yahoo!_mindset',
'yandex',
'flexum',
'yanga',
'yooglifetchagent',
'z\-add_link_checker',
'zealbot',
'zhuaxia',
'zspider',
'zeus',
'ng\/1\.', # put at end to avoid false positive
'ng\/2\.', # put at end to avoid false positive
'exabot',  # put at end to avoid false positive
# Other id that are 99% of robots
'wget',
'libwww\-perl',
'w3c\-webcon',
'lwp',
'java\/[0-9]',  # put at end to avoid false positive
'uri::fetch'
);
@RobotsSearchIDOrder_listgen = (
# Generic robot
'robot',
'checker',
'crawl',
'discovery',
'hunter',
'scanner',
'spider',
'sucker',
'bot[\s_+:,\.\;\/\\\-]',
'[\s_+:,\.\;\/\\\-]bot',
'no_user_agent'
);



# RobotsHashIDLib
# List of robots names ('robot id','robot clear text')
#-------------------------------------------------------
%RobotsHashIDLib   = (
# Japanese Robots+alpha
# Added by Ryu 2006.03.15
'another_html\-lint','Another HTML-lint',
'adsenserbot','<a href="http://adsenser.jp">AdSenserBot</a>',
'adsbot\-google','<a href="http://www.google.com/adsbot.html">Google AdWords Bot</a>',
'amfibibot','Amfibibot',
'baiduimagespider','<a href="http://www.baidu.com/search/spider.html">BaiduImageSpider</a>',
'baidumobaider','<a href="http://www.baidu.jp/spider/">BaiduMobaider</a>',
'becomejpbot','<a href="http://www.become.co.jp/site_owner.html">BecomeJPBot</a>',
'bibliotheca','<a href="http://www.hitachi.co.jp/Prod/comp/soft1/textsearch/product/component/bib21/">Bibliotheca</a>',
'bizpal\srss\saggregator','<a href="http://bizpal.jp">BizPal RSS Aggregator</a>',
'bookmark\srenewal\scheck\sagent','<a href="http://www.bookmark.ne.jp">Bookmark Renewal Check Agent</a>',
'camelstampede','CamelStampede',
'cazoodlebot','<a href="http://www.cazoodle.com">CazoodleBot</a>',
'charlotte','<a href="http://www.searchme.com/support/">Charlotte</a>',
'coast\swebmaster','COAST WebMaster',
'cococ','<a href="http://am13.net/wiki/index.php?cococ">cococ</a>',
'comaneci_bot','<a href="http://help.i-know.jp/?crawler">Comaneci bot</a>',
'crooz','DoCoMo CROOZ',
'daumoa','<a href="http://www.daum.net">DAUMOA</a>',
'dealgates\sbot','<a href="http://spider.dealgates.com/bot.html">DealGates Bot</a>',
'depspid\/','<a href="http://about.depspid.net">DepSpid</a>',
'diffbrowser\/','<a href="http://www010.upp.so-net.ne.jp/suede/diffbrowser.html">DiffBrowser</a>',
'dotbot','<a href="http://www.dotnetdotcom.org">Dotbot</a>',
'ec_favorite','eC_favorite',
'empas_robot','EMPAS ROBOT',
'e\-societyrobot','<a href="http://www.yama.info.waseda.ac.jp/~yamana/es/">e-SocietyRobot</a>',
'everyfeed-spider','Everyfeed Spider',
'faedit\/','<a href="http://www.srcw.net/wiki/index.php?FaEdit">FaEdit</a>',
'fast\smetaweb\scrawler','<a href="http://fastsearch.com">FAST MetaWeb Crawler</a>',
'fastladder\sfeedfetcher','<a href="http://fastladder.com">Fastladder FeedFetcher</a>',
'feed24\.com','<a href="http://feed24.com">Feed24.com</a>',
'feedbringer','<a href="http://feedbringer.net" title="Bot home page">FEEDBRINGER</a>',
'feedchecker','FeedChecker',
'feed\sparser','<a href="http://rss-search.net">Feed Parser</a>',
'feedpath','<a href="http://feedpath.jp" title="Bot home page">Feedpath</a>',
'feedshow','<a href="http://www.feedshow.com" title="Bot home page">Feedshow</a>',
'fly\/','fly sinet.ad.jp',
'freshreader','<a href="http://www.freshreader.com" title="Bot home page">FreshReader</a>',
'fsbot','<a href="http://www.adin.co.jp/fs/">Flex Search</a>',
'g10\sreader','<a href="http://wordg10.com" title="Bot home page">G10 Reader</a>',
'gingercrawler','<a href="http://www.gingersoftware.com/crawler_agent.htm" title="Bot home page">GingerCrawler</a>',
'goorssreader','goo RSS Reader',
'grub\-client','Grub Client',
'gsa\-crawler','<a href="http://www.google.com/enterprise/gsa/index.html" title="Bot home page">GSA Crawler</a>',
'hatena','<a href="http://a.hatena.ne.jp/help" title="Bot home page">はてなアンテナ</a>',
'headline\-reader','Headline-Reader',
'hobbit\sbbtest\-net','<a href="http://hobbitmon.sourceforge.net" title="Bot home page">Hobbit bbtest-net</a>',
'html\sget','HTML Get(SPAM)',
'hyperestraier','<a href="http://hyperestraier.sourceforge.net/index.html" title="Bot home page">HyperEstraier</a>',
'hyperrobot','HyperRobot InfoWeb',
'icc\-crawler','<a href="http://kc.nict.go.jp/icc/crawl-ja.html">ICC-Crawler</a>',
'ilissurf','<a href="http://software.fujitsu.com/jp/ilis_univ/surf/" title="iLisSurf - FUJITSU Japan">iLisSurf</a>',
'indexpert','indexpert',
'internal\sdummy\sconnection','internal dummy connection for <a href="http://httpd.apache.org/docs/2.0/mod/mod_dav.html" title="Apache モジュール mod_dav">Apache WebDAV</a>',
'jetbot','<a href="http://www.jetrun.jp">jetbot</a>',
'kb\.rmail','<a href="http://www.r-mail.org">kb.Rmail</a>',
'keywalkerbot','<a href="http://www.keywalker.co.jp/crawl/bot.html">Keywalkerbot</a>',
'klsh\-pageget\/','<a href="http://www.kondo-net.gr.jp/klsh/">KLSH-PageGet</a>',
'kotoha\.co\.jp','コトハコ',
'libghttp\/','libghttp(SPAM?)',
'livedoorcheckers','Livedoor Checkers',
'livedoor\sfeedfetcher','<a href="http://reader.livedoor.com" title="Bot home page">livedoor FeedFetcher</a>',
'livedoor\sscreenshot\/','<a href="http://reader.livedoor.com" title="Bot home page">livedoor ScreenShot</a>',
'livedoor\shttpclient\/','livedoor HttpClient',
'maldive\scrawler','Maldive crawler',
'masagool','<a href="http://sagool.jp">MaSagool</a>',
'metalogger','Metalogger',
'mfcrawler','MFcrawler',
'mogimogi\/','mogimogi',
'mlbot','<a href="http://www.metadatalabs.com">MLBot</a>',
'mqbot','<a href="http://metaquerier.cs.uiuc.edu">MQbot</a>',
'msr\-isrccrawler','MSR-ISRCCrawler',
'multicrawler','<a href="http://sw.deri.org/2006/04/multicrawler/robots.html">MultiCrawler</a>',
'naver','NaverBot',
'niyonizer','NIYONIZER',
'ndl\-japan\-research\-robot','国立国会図書館',
'netresearchserver','<a href="http://loopimprovements.com/robot.html">Net Research Server (NRS)</a>',
'nettracker','NetTracker',
'netvibes','<a href="http://www.netvibes.com" title="Bot home page">Netvibes</a>',
'newsalloy','<a href="http://www.NewsAlloy.com" title="Bot home page">NewsAlloy</a>',
'^obot','oBot',
'onetszukaj','<a href="http://szukaj.onet.pl">OnetSzukaj</a>',
'openbot','Openfind data gatherer',
'oracle\ssecure\senterprise\ssearch','Oracle Secure Enterprise Search',
'outfoxbot','<a href="http://www.yodao.com/help/webmaster/spider/" title="YodaoBot">old OutfoxBot</a>',
'page_verifier','<a href="http://www.securecomputing.com/PageVerifier.cfm">page_verifier</a>',
'pear\shttp_request\sclass','<a href="http://pear.php.net">PEAR HTTP_Request class</a>',
'paipo\-bot','<a href="http://paipo.jp">PAIPO-Bot</a>',
'pathtraq','<a href="http://pathtraq.com/about">Pathtraq</a>',
'pingdom\sgigrib','<a href="http://www.pingdom.com">Pingdom GIGRIB</a>',
'pipeliner','PipeLine Spider',
'playon\srss\sreader\/','<a href="http://playon.jp/rss/">PLAYON RSS READER</a>',
'pockey\-gethtml','Pockey GetHTML',
'protopage\/','<a href="http://www.protopage.com">Protopage</a>',
'research\-spider','<a href="http://www.freedownloadscenter.com/Network_and_Internet/Web_Searching_Tools/Research_Spider.html">Research Spider</a>',
'search\-hp_bot','<a href="http://search-hp.com" title="Bot home page">search-hp_bot</a>',
'seo\.cug\.net\slink\schecker','<a href="http://seo.cug.net">seo.cug.net link checker</a>',
'shopwiki\/','<a href="http://www.shopwiki.com/wiki/Help:Bot">ShopWiki</a>',
'smart\.apnoti\.com','<a href="http://smart.apnoti.com/index/aboutApnotiWebCrawler">smart.apnoti.com Robot</a>',
'snapbot','Snapbot',
'snoopy\sv','<a href="http://sourceforge.net/projects/snoopy/">Snoopy</a>',
'sogou\sweb\sspider','<a href="http://www.sogou.com/docs/help/webmasters.htm#07">Sogou web spider</a>',
'sonar\/','<a href="http://boxer.ne.jp/product_list/sonar/">Sonar Crawler</a>', #Added by toshi 2006.04.09
'sonarplus\/','<a href="http://boxer.ne.jp/product_list/sonar_plus/">Sonar PLUS Crawler</a>',
'spamrobot@126\.com','126.com',
'sproose\/','<a href="http://www.sproose.com/bot.html" title="Bot home page">sproose bot</a>',
'stackrambler','StackRambler',
'statbot@gmail\.com','Gmail Com (Google)',
'strategic\sboard\sbot','<a href="http://www.strategicboard.com" title="Bot home page">Strategic Board Bot</a>',
'technoratisnoop','TechnoratiSnoop(SPAM?)',
'techrigybot','<a href="http://www.techrigy.com" title="Bot home page">TechrigyBot</a>',
'tencenttraveler','TencentTraveler', 	# Must be before msiecrawler.
'trackback\/','TrackBack(SPAM?)',
'umn\/','<a href="http://www.nori-s.net/soft/umn/">URLマネージャ</a>',
'useragent','USERAGENT(SPAM?)',
'w3crobot','Webbot - the Libwww Robot',
'wadaino\.jp\-crawler','<a href="http://wadaino.jp">話題の.jpクローラー</a>',
'webalta','<a href="http://www.webalta.net/ru/about_webmaster.html">WebAlta Crawler</a>',
'webauto','<a href="http://www.yanasoft.co.jp/webautodoc.html">WebAuto</a>',
'webaroobot','<a href="http://www.webaroo.com/rooSiteOwners.html">Webaroo Bot</a>',
'webdigity\swhois\sservice','<a href="http://www.webdigity.com/ws/">webdigity whois service</a>',
'website\sexplorer','<a href="http://www.umechando.com/webex/">Website Explorer</a>',
'wish\-la','<a href="http://wish.slis.tsukuba.ac.jp/jp/">Wish Project(wish la)</a>',
'wish\-project','<a href="http://wish.slis.tsukuba.ac.jp/jp/">Wish Project</a>',
'wiwi','<a href="http://wi2.jp">WiWi</a>',
'wwwster\/','<a href="mailto:gue@cis.uni-muenchen.de">wwwster</a>',
'yahoo!-adcrawler','<a href="http://help.yahoo.com/yahoo_adcrawler">Yahoo! Ad Crawler</a>',
'yeti\/','Yeti',
'zao\-crawler','Zao Crawler',
'zibber','<a href="http://www.zibb.com/CrawlerInformaion.aspx">Zibb Crawler</a>',
'^-$','-(SPAM?)',
# Common robots (In robot file)
'appie','<a href="http://www.walhello.com" title="Bot home page">Walhello appie</a>',
'architext','ArchitextSpider',
'jeeves\/','<a href="http://about.ask.com/en/docs/about/webmasters.shtml" title="Bot home page">Ask</a>',
'bjaaland','Bjaaland',
'ferret','Wild Ferret Web Hopper #1, #2, #3',
'contentmatch','<a href="http://p4p.cn.yahoo.com">Yahoo!China ContentMatch Crawler</a>',
'googlebot','<a href="http://www.google.com/bot.html" title="Bot home page">Googlebot</a>',
'google\-sitemaps', 'Google Sitemaps',
'gulliver','Northern Light Gulliver',
'virus\_detector','<a href="http://www.securecomputing.com" title="virus_harvester@securecomputing.com; Bot home page">virus_detector</a>',
'harvest','Harvest',
'htdig','ht://Dig',
'linkwalker','LinkWalker',
'lilina','Lilina',
'lycos_','Lycos',
'moget','moget',
'muscatferret','Muscat Ferret',
'myweb','Internet Shinchakubin',
'nomad','Nomad',
'scooter','Scooter',
'slurp','<a href="http://help.yahoo.com/help/us/ysearch/slurp/" title="Bot home page">Yahoo Slurp</a>',
'^voyager\/','Voyager',
'weblayers','Weblayers',
# Common robots (Not in robot file)
'antibot','Antibot',
'bruinbot','<a href="http://web.archive.org" title="BruinBot home page">The web archive</a>',
'digout4u','Digout4u',
'echo!','EchO!',
'fast\-webcrawler','Fast-Webcrawler',
'ia_archiver\-web\.archive\.org','<a href="http://web.archive.org" title="Bot home page">The web archive (IA Archiver)</a>',
'ia_archiver','<a href="http://www.alexa.com" title="Bot home page">Alexa (IA Archiver)</a>',
'jennybot','JennyBot',
'mercator','Mercator',
'msnbot\-media','<a href="http://search.msn.com/msnbot.htm" title="Bot home page">MSNBot-media</a>',
'msnbot','<a href="http://search.msn.com/msnbot.htm" title="Bot home page">MSNBot</a>',
'netcraft','<a href="http://www.netcraft.com/survey/" title="Bot home page">Netcraft</a>',
'petersnews','Petersnews',
'unlost_web_crawler','Unlost Web Crawler',
'voila','Voila',
'webbase', 'WebBase',
'zyborg','<a href="http://www.WISEnutbot.com" title="wn-14.zyborg@looksmart.net Bot home page">ZyBorg</a>',
'wisenutbot','<a href="http://www.WISEnutbot.com" title="Bot home page">WISENutbot</a>',
'webcollage','<a href="http://www.jwz.org/webcollage/" title="WebCollage home page">WebCollage</a>',
'cfetch','<a href="http://www.kosmix.com/crawler.html" title="kosmix home page">Cfetch</a>',
# Less common robots (In robot file)
'[^a]fish','Fish search',
'abcdatos','ABCdatos BotLink',
'acme\.spider','Acme.Spider',
'ahoythehomepagefinder','Ahoy! The Homepage Finder',
'alkaline','Alkaline',
'anthill','Anthill',
'arachnophilia','Arachnophilia',
'arale','Arale',
'araneo','Araneo',
'aretha','Aretha',
'ariadne','ARIADNE',
'powermarks','<a href="http://www.kaylon.com/power.html" title="Bot home page">Powermarks</a>', # must come before Arks; seen used by referrer spam
'arks','arks',
'aspider','ASpider (Associative Spider)',
'atn\.txt','ATN Worldwide',
'atomz','Atomz.com Search Robot',
'auresys','AURESYS',
'backrub','BackRub',
'bbot','BBot',
'bigbrother','Big Brother',
'blackwidow','BlackWidow',
'blindekuh','Die Blinde Kuh',
'bloodhound','Bloodhound',
'borg\-bot','Borg-Bot',
'brightnet','bright.net caching robot',
'bspider','BSpider',
'cactvschemistryspider','CACTVS Chemistry Spider',
'calif[^r]','Calif',
'cassandra','Cassandra',
'cgireader','Digimarc Marcspider/CGI',
'checkbot','Checkbot',
'christcrawler','ChristCrawler.com',
'churl','churl',
'cienciaficcion','cIeNcIaFiCcIoN.nEt',
'collective','Collective',
'combine','Combine System',
'conceptbot','Conceptbot',
'coolbot','CoolBot',
'core','Web Core / Roots',
'cosmos','XYLEME Robot',
'cruiser','Internet Cruiser Robot',
'cusco','Cusco',
'cyberspyder','CyberSpyder Link Test',
'desertrealm','Desert Realm Spider',
'deweb','DeWeb(c) Katalog/Index',
'dienstspider','DienstSpider',
'digger','Digger',
'diibot','Digital Integrity Robot',
'direct_hit','Direct Hit Grabber',
'dnabot','DNAbot',
'download_express','DownLoad Express',
'dragonbot','DragonBot',
'dwcp','DWCP (Dridus\' Web Cataloging Project)',
'e\-collector','e-collector',
'ebiness','EbiNess',
'elfinbot','ELFINBOT',
'emacs','Emacs-w3 Search Engine',
'emcspider','ananzi',
'esther','Esther',
'evliyacelebi','Evliya Celebi',
'fastcrawler','FastCrawler',
'feedcrawl','FeedCrawl by feed@aobo.com',
'fdse','Fluid Dynamics Search Engine robot',
'felix','Felix IDE',
'fetchrover','FetchRover',
'fido','fido',
'finnish','Hämähäkki',
'fireball','KIT-Fireball',
'fouineur','Fouineur',
'francoroute','Robot Francoroute',
'freecrawl','Freecrawl',
'funnelweb','FunnelWeb',
'gama','gammaSpider, FocusedCrawler',
'gazz','gazz',
'gcreep','GCreep',
'getbot','GetBot',
'geturl','GetURL',
'golem','Golem',
'gougou','GouGou',
'grapnel','Grapnel/0.01 Experiment',
'griffon','Griffon',
'gromit','Gromit',
'gulperbot','Gulper Bot',
'hambot','HamBot',
'havindex','havIndex',
'hometown','Hometown Spider Pro',
'htmlgobble','HTMLgobble',
'hyperdecontextualizer','Hyper-Decontextualizer',
'iajabot','iajaBot',
'iaskspider','<a href="http://www.iask.com">Sina Iask Spider</a>',
'hl_ftien_spider','<a href="http://www.hylanda.com">Hylanda</a>',
'sogou','<a href="http://www.sogou.com">Sogou Spider</a>',
'iconoclast','Popular Iconoclast',
'ilse','Ingrid',
'imagelock','Imagelock',
'incywincy','IncyWincy',
'informant','Informant',
'infoseek','InfoSeek Robot 1.0',
'infoseeksidewinder','Infoseek Sidewinder',
'infospider','InfoSpiders',
'inspectorwww','Inspector Web',
'intelliagent','IntelliAgent',
'irobot','I, Robot',
'iron33','Iron33',
'israelisearch','Israeli-search',
'javabee','JavaBee',
'jbot','JBot Java Web Robot',
'jcrawler','JCrawler',
'jobo','JoBo Java Web Robot',
'jobot','Jobot',
'joebot','JoeBot',
'jubii','The Jubii Indexing Robot',
'jumpstation','JumpStation',
'kapsi','image.kapsi.net',
'katipo','Katipo',
'kilroy','Kilroy',
'ko_yappo_robot','KO_Yappo_Robot',
'kummhttp','<a href="http://www.psychedelix.com/cgi-bin/csv2html.pl?data=allagents.csv&amp;template=detail.html&amp;match=\bid_g_l_301105_2\b" title="Bot documentation page">KummHttp</a>',
'labelgrabber\.txt','LabelGrabber',
'larbin','<a href="http://para.inria.fr/~ailleret/larbin/index-eng.html" title="Bot home page">larbin</a>',
'legs','legs',
'linkidator','Link Validator',
'linkscan','LinkScan',
'lockon','Lockon',
'logo_gif','logo.gif Crawler',
'macworm','Mac WWWWorm',
'lmspider','<a href="http://www.nuance.com" title="Bot home page lmspider@scansoft.com">lmspider</a>',
'lwp\-request','<a href="http://search.cpan.org/~gaas/libwww-perl-5.69/bin/lwp-request" title="lwp-request home page">lwp-request</a>',
'lwp\-trivial','<a href="http://search.cpan.org/src/GAAS/libwww-perl-5.805/lib/LWP/Simple.pm" title="lwp-trivial home page">lwp-trivial</a>',
'magpie','<a href="http://magpierss.sf.net" title="Bot home page">MagpieRSS</a>',
'marvin','marvin/infoseek',
'mattie','Mattie',
'mediafox','MediaFox',
'merzscope','MerzScope',
'meshexplorer','NEC-MeshExplorer',
'mindcrawler','MindCrawler',
'mnogosearch','mnoGoSearch search engine software',
'momspider','MOMspider',
'monster','Monster',
'motor','Motor',
'muncher','Muncher',
'mwdsearch','Mwd.Search',
'ndspider','NDSpider',
'nederland\.zoek','Nederland.zoek',
'netcarta','NetCarta WebMap Engine',
'netmechanic','<a href="http://www.netmechanic.com" title="Bot home page">NetMechanic</a>',
'netscoop','NetScoop',
'newscan\-online','newscan-online',
'nhse','NHSE Web Forager',
'northstar','The NorthStar Robot',
'nzexplorer','nzexplorer',
'objectssearch','ObjectsSearch',
'occam','Occam',
'octopus','HKU WWW Octopus',
'openfind','Openfind data gatherer',
'orb_search','Orb Search',
'packrat','Pack Rat',
'pageboy','PageBoy',
'parasite','ParaSite',
'patric','Patric',
'pegasus','pegasus',
'perignator','The Peregrinator',
'perlcrawler','PerlCrawler 1.0',
'phantom','Phantom',
'phpdig','PhpDig',
'piltdownman','PiltdownMan',
'pimptrain','Pimptrain.com\'s robot',
'pioneer','Pioneer',
'pitkow','html_analyzer',
'pjspider','Portal Juice Spider',
'plumtreewebaccessor','PlumtreeWebAccessor',
'poppi','Poppi',
'portalb','PortalB Spider',
'psbot','<a href="http://www.picsearch.com/bot.html" title="Bot home page">psbot</a>',
'python','<a href="http://docs.python.org/library/urllib.html" title="Tools developed using a Python library">Python-urllib</a>',
'raven','Raven Search',
'rbse','RBSE Spider',
'resumerobot','Resume Robot',
'rhcs','RoadHouse Crawling System',
'road_runner','Road Runner: The ImageScape Robot',
'robbie','Robbie the Robot',
'robi','ComputingSite Robi/1.0',
'robocrawl','RoboCrawl Spider',
'robofox','RoboFox',
'robozilla','Robozilla',
'roverbot','Roverbot',
'rules','RuLeS',
'safetynetrobot','SafetyNet Robot',
'search\-info','Sleek',
'search_au','Search.Aus-AU.COM',
'searchprocess','SearchProcess',
'senrigan','Senrigan',
'sgscout','SG-Scout',
'shaggy','ShagSeeker',
'shaihulud','Shai\'Hulud',
'sift','Sift',
'simbot','Simmany Robot Ver1.0',
'site\-valet','Site Valet',
'sitetech','SiteTech-Rover',
'skymob','Skymob.com',
'slcrawler','SLCrawler',
'smartspider','Smart Spider',
'snooper','Snooper',
'solbot','Solbot',
'speedy','<a href="http://www.entireweb.com/about/search_tech/speedyspider/" title="Speedy Spider home page">Speedy Spider</a>',
'spider_monkey','Spider monkey',
'spiderbot','SpiderBot',
'spiderline','Spiderline Crawler',
'spiderman','<a href="http://www.iscrawling.com" title="Spiderman home page">Spiderman</a>',
'spiderview','SpiderView&trade;',
'spry','Spry Wizard Robot',
'ssearcher','Site Searcher',
'sqworm','<a href="http://www.websense.com" title="Bot home page (source: http://www.pgts.com.au)">Sqworm</a>',
'suke','<a href="http://kensaku.org" title="Bot home page">Suke</a>',
'sunrise','<a href="http://www.sunrisexp.com" title="Sunrise home page">Sunrise</a>',
'suntek','suntek search engine',
'sven','Sven',
'tach_bw','TACH Black Widow',
'tagyu_agent','<a href="http://www.tagyu.com" title="Bot home page">Tagyu Agent</a>',
'tarantula','Tarantula',
'tarspider','tarspider',
'tailrank','<a href="http://tailrank.com/robot">TailRank</a>',
'techbot','TechBOT',
'templeton','Templeton',
'titan','TITAN',
'titin','TitIn',
'tkwww','The TkWWW Robot',
'tlspider','TLSpider',
'ucsd','UCSD Crawl',
'udmsearch','UdmSearch',
'universalfeedparser','<a href="http://feedparser.org" title="Bot home page">UniversalFeedParser</a>',
'urlck','URL Check',
'valkyrie','Valkyrie',
'verticrawl','Verticrawl',
'victoria','Victoria',
'visionsearch','vision-search',
'voidbot','void-bot',
'vwbot','VWbot',
'w3index','The NWI Robot',
'w3m2','W3M2',
'wallpaper','WallPaper (alias crawlpaper)',
'wanderer','the World Wide Web Wanderer',
'wapspider','w@pSpider by wap4.com',
'webbandit','WebBandit Web Spider',
'webcatcher','WebCatcher',
'webcopy','WebCopy',
'webfetcher','webfetcher',
'webfoot','The Webfoot Robot',
'webinator','Webinator',
'weblinker','WebLinker',
'webmirror','WebMirror',
'webmoose','The Web Moose',
'webquest','WebQuest',
'webreader','Digimarc MarcSpider',
'webreaper','WebReaper',
'websnarf','Websnarf',
'webspider','WebSpider',
'webvac','WebVac',
'webwalk','webwalk',
'webwalker','WebWalker',
'webwatch','WebWatch',
'whatuseek','whatUseek Winona',
'whowhere','WhoWhere Robot',
'wired\-digital','Wired Digital',
'wmir','w3mir',
'wolp','WebStolperer',
'wombat','The Web Wombat',
'wordpress','<a href="http://wordpress.org" title="WordPress home page">WordPress</a>',
'worm','The World Wide Web Worm',
'woozweb','Woozweb Monitoring',
'wwwc','WWWC',
'wz101','WebZinger',
'xget','XGET',
# Other robots reported by users
'1\-more_scanner','<a href="http://www.myzips.com/software/1-More-Scanner.phtml" title="1-More Scanner home page">1-More Scanner</a>',
'accoona\-ai\-agent','<a href="http://www.accoona.com" title="Accoona-AI-Agent home page">Accoona-AI-Agent</a>',
'activebookmark','<a href="http://www.libmaster.com/active_bookmark.php" title="ActiveBookmark home page">ActiveBookmark</a>',
'adamm_bot','<a href="http://home.blic.net/adamm/" title="Bot home page">AdamM Bot</a>',
'almaden','<a href="http://www.almaden.ibm.com/cs/crawler" title="IBM Almaden Research Center WebFountain&trade; Bot home page">IBM Almaden</a> Research Center WebFountain&trade;',
'aipbot','<a href="http://www.aipbot.com" title="aipbot@aipbot.com Bot home page">aipbot</a>',
'aleadsoftbot','<a href="http://www.aleadsoft.com/bot.htm" title="ALeadSoftbot home page">ALeadSoftbot</a>',
'alpha_search_agent','Alpha Search Agent',
'allrati','Allrati',
'aport', 'Aport',
'archive\.org_bot','<a href="http://crawls.archive.org/collections/bncf/crawl.html" title="Bot home page">archive.org bot</a>',
'argus','<a href="http://www.simpy.com/bot.html" title="feedback@simpy.com Bot home page">Argus</a>',
'arianna\.libero\.it','<a href="http://arianna.libero.it" title="Bot home page">arianna.libero.it</a>',
'aspseek','<a href="http://www.aspseek.org" title="Bot home page">ASPseek</a>',
'asterias', 'Asterias',
'awbot', 'AWBot',
'baiduspider','<a href="http://www.baidu.com/search/spider.html" title="Bot home page">BaiDuSpider</a>',
'becomebot', '<a href="http://www.become.com/site_owners.html" title="Bot home page">BecomeBot</a>',
'bender','<a href="http://bender.ucr.edu" title="Bot home page">bender</a> <a href="http://ivia.ucr.edu/manuals/NiFC/current/index.shtml" title="Bot home page">focused_crawler</a>',
'betabot','BetaBot',
'biglotron','<a href="http://www.biglotron.com/robot.html" title="Bot home page">Biglotron</a>',
'bittorrent_bot','<a href="http://www.bittorrent.com" title="Bot home page">BitTorrent Bot</a>',
'biz360\sspider','<a href="http://www.biz360.com" title="blogsmanager@biz360.com Bot home page">Biz360 spider</a>',
'blogbridge\sservice','<a href="http://www.blogbridge.com" title="Bot home page">BlogBridge Service</a>',
'bloglines','<a href="http://www.bloglines.com" title="Bot home page">Bloglines</a>',
'blogpulse','<a href="http://www.intelliseek.com" title="Bot home page">BlogPulse ISSpider intelliseek.com</a>',
'blogsearch','<a href="http://www.icerocket.com" title="Bot home page">BlogSearch</a>',
'blogshares','<a href="http://blogshares.com/help.php?node=7" title="Bot home page">Blogshares Spiders</a>',
'blogslive','<a href="http://www.blogslive.com" title="info@blogslive.com Bot home page">Blogslive</a>',
'blogssay','<a href="http://www.blogssay.com" title="Bot home page">BlogsSay :: RSS Search Crawler</a>',
'bncf\.firenze\.sbn\.it\/raccolta\.txt','<a href="http://www.bncf.firenze.sbn.it/raccolta.txt" title="Bot home page">Biblioteca Nazionale Centrale di Firenze</a>',
'bobby', 'Bobby',
'boitho\.com\-dc','<a href="http://www.boitho.com/dcbot.html" title="Bot home page">boitho.com-dc</a>',
'bookmark\-manager','<a href="http://bkm.sourceforge.net" title="Bookmark-Manager home page">Bookmark-Manager</a>',
'boris', 'Boris',
'bumblebee', 'Bumblebee (relevare.com)',
'candlelight\_favorites\_inspector','<a href="http://www.candlelight.com/home.html" title="Candlelight_Favorites_Inspector  home page">Candlelight_Favorites_Inspector</a>',
'cbn00glebot','cbn00glebot',
'cerberian\sdrtrs','<a href="http://www.pgts.com.au/cgi-bin/psql?robot_info=25240" title="Bot home page">Cerberian Drtrs</a>',
'cfnetwork','<a href="http://www.cocoadev.com/index.pl?CFNetwork" title="CFNetwork home page">CFNetwork</a>',
'cipinetbot','<a href="http://www.cipinet.com/bot.html" title="CipinetBot home page">CipinetBot</a>',
'checkweb_link_validator','<a href="http://p.duby.free.fr/chkweb.htm" title="CheckWeb link validator home page">CheckWeb link validator</a>',
'commons\-httpclient','<a href="http://jakarta.apache.org/commons/httpclient/" title="Bot home page">Jakarta commons-httpclient</a>',
'computer_and_automation_research_institute_crawler','<a href="http://www.ilab.sztaki.hu/~stamas/publications/p184-benczur.html" title="Computer and Automation Research Institute Crawler home page">Computer and Automation Research Institute Crawler</a>',
'converamultimediacrawler','<a href="http://www.authoritativeweb.com/crawl/" title="ConveraMultiMediaCrawler home page">ConveraMultiMediaCrawler</a>',
'converacrawler','<a href="http://www.authoritativeweb.com/crawl/" title="ConveraCrawler home page">ConveraCrawler</a>',
'cscrawler','CsCrawler',
'cse_html_validator_lite_online','<a href="http://online.htmlvalidator.com/php/onlinevallite.php" title="CSE HTML Validator Lite Online home page">CSE HTML Validator Lite Online</a>','cuasarbot','<a href="http://www.cuasar.com" title="Cuasarbot home page">Cuasarbot</a>',
'cursor','<a href="http://adcenter.hu/docs/en/bot.html " title="Cursor home page">Cursor</a>',
'custo','<a href="http://www.netwu.com/custo/" title="Custo home page">Custo</a>',
'datafountains\/dmoz_downloader','<a href="http://infomine.ucr.edu/ " title="DataFountains/DMOZ Downloader home page">DataFountains/DMOZ Downloader</a>',
'daviesbot', 'DaviesBot',
'daypopbot', 'DayPop',
'deepindex','<a href="http://www.deepindex.net/faq.php" title="Deepindex home page">Deepindex</a>',
'dipsie\.bot','<a href="http://www.dipsie.com/bot/" title="Bot home page">Dipsie</a>',
'dnsgroup','<a href="http://www.dnsgroup.com" title="DNSGroup home page">DNSGroup</a>',
#'docomo','<a href="http://www.nttdocomo.co.jp" title="DoCoMo home page">DoCoMo</a>',
'domainchecker','<a href="http://net-promoter.com" title="DomainChecker home page (not confirmed)">DomainChecker</a>',
'domainsdb\.net','<a href="http://domainsdb.net" title="Bot home page">DomainsDB.net</a>',
'dulance','<a href="http://www.dulance.com/bot.jsp" title="Bot home page">Dulance</a>',
'dumbot','<a href="http://www.dumbfind.com" title="Dumbot home page">Dumbot</a>',
'dumm\.de\-bot','<a href="http://www.dumm.de" title="dumm.de-Bot home page">dumm.de-Bot</a>',
'earthcom\.info','<a href="http://www.earthcom.info" title="Bot home page">EARTHCOM.info</a>',
'easydl','<a href="http://keywen.com/Encyclopedia/Bot/" title="EasyDL  home page">EasyDL</a>',
'edgeio\-retriever','<a href="http://www.edgeio.com" title="Bot home page">edgeio-retriever</a>',
'ets_v','<a href="http://www.freetranslation.com/help/" title="ETS home page">ETS</a> Enterprise Translation Server',
'exactseek','ExactSeek Crawler',
'extreme\_picture\_finder','<a href="http://www.exisoftware.com" title="Extreme_Picture_Finder home page">Extreme_Picture_Finder</a>',
'eventax','<a href="http://www.eventax.de" title="eventax home page">eventax</a>',
'everbeecrawler','EverbeeCrawler',
'everest\-vulcan','<a href="http://everest.vulcan.com/crawlerhelp" title="Bot home page">Everest-Vulcan</a>',
'ezresult', 'Ezresult',
'enteprise','<a href="http://www.fastsearch.com" title="Bot home page">Fast Enteprise Crawler</a>',
'facebook','FaceBook bot',
'fast\-search\-engine','<a href="http://www.fast-search-engine.com" title="Bot home page">Fast-Search-Engine</a> (not fastsearch.com)',
'fast\senterprise\scrawler','<a href="http://www.fast.no" title="FAST Enterprise Crawler home page">FAST Enterprise Crawler</a>',
'fast_enterprise_crawler.*scrawleradmin\.t\-info@telekom\.de','<a href="http://www.telekom.de" title="FAST Enterprise Crawler * crawleradmin.t-info@telekom.de home page">FAST Enterprise Crawler * crawleradmin.t-info@telekom.de</a>',
'matrix_s\.p\.a\._\-_fast_enterprise_crawler','<a href="http://tin.virgilio.it" title="Matrix S.p.A. - FAST Enterprise Crawler home page">Matrix S.p.A. - FAST Enterprise Crawler</a>',
'fast_enterprise_crawler.*t\-info_bi_cluster_crawleradmin\.t\-info@telekom\.de','<a href="http://www.telekom.de" title="FAST Enterprise Crawler * T-Info_BI_cluster crawleradmin.t-info@telekom.de home page">FAST Enterprise Crawler * T-Info_BI_cluster crawleradmin.t-info@telekom.de</a>',
'favicon','FavIconizer',
'favorg','<a href="http://www.pcmag.com/article2/0,4149,108438,00.asp" title="FavOrg home page">FavOrg</a>',
'favorites_sweeper','<a href="http://www.manitools.com/favsweep/" title="Favorites_Sweeper home page">Favorites Sweeper</a>',
'feedburner', 'Feedburner',
'feedfetcher\-google','<a href="http://www.google.com/feedfetcher.html" title="Bot home page">Feedfetcher-Google</a>',
'feedflow','<a href="http://feedflow.com/about" title="Bot home page">FeedFlow</a>',
'feedster','<a href="http://www.feedster.com" title="Bot home page">Feedster</a>',
'feedsky','<a href="http://www.feedsky.com" title="Bot home page">FeedSky</a>',
'feedvalidator','<a href="http://feedvalidator.org" title="FeedValidator home page">FeedValidator</a>',
'filmkamerabot','<a href="http://www.filmkamera.at/bot.html" title="FilmkameraBot home page">FilmkameraBot</a>',
'findexa_crawler','<a href="http://www.findexa.no/gulesider/article26548.ece " title="Findexa Crawler home page">Findexa Crawler</a>',
'geniebot','<a href="http://www.genieknows.com" title="Bot home page">Geniebot</a>',
'findlinks','<a href="http://wortschatz.uni-leipzig.de/findlinks/" title="Bot home page">Findlinks</a>',
'fooky\.com\/ScorpionBot','<a href="http://www.fooky.com/scorpionbots" title="Fooky.com/ScorpionBot/ScoutOut home page">Fooky.com/ScorpionBot/ScoutOut</a>',
'g2crawler','<a href="http://crawler.instantnetworks.net" title="Bot home page (nobody@airmail.net)">G2Crawler</a>',
'gaisbot','<a href="http://gais.cs.ccu.edu.tw/robot.php" title="Bot home page">Gaisbot</a>',
'gigabot','<a href="http://www.gigablast.com/spider.html" title="Bot home page">GigaBot</a>',
'girafabot','<a href="http://www.girafa.com" title="Bot home page">Girafabot</a>',
'global_fetch','<a href="http://www.wesonet.com" title="Global Fetch home page">Global Fetch</a>',
'gnodspider','GNOD Spider',
'goforit\.com','<a href="http://www.goforit.com/about/" title="GoForIt.com home page">GoForIt.com</a>',
'goforitbot','<a href="http://www.goforit.com/about/" title="GOFORITBOT home page">GOFORITBOT</a>',
'gpu_p2p_crawler','<a href="http://gpu.sourceforge.net/search_engine.php" title="Bot home page">GPU p2p crawler</a>',
'grub','<a href="http://www.grub.org">Grub.org</a>',
'henrythemiragorobot', '<a href="http://www.miragorobot.com/scripts/mrinfo.asp" title="Bot home page">Mirago</a>',
'heritrix','<a href="http://crawler.archive.org" title="(used by a few different companies) Bot home page">Heritrix</a>',
'holmes', 'Holmes',
'hoowwwer','<a href="http://cosco.hiit.fi/search/hoowwwer/" title="HooWWWer home page">HooWWWer</a>',
'hpprint','HPPrint',
'htmlparser','<a href="http://htmlparser.sourceforge.net" title="HTMLParser home page">HTMLParser</a>',
'html\_link\_validator','<a href="http://www.lithopssoft.com " title="Html_Link_Validator home page">Html_Link_Validator</a>',
'httrack','<a href="http://www.httrack.com" title="Bot home page">HTTrack off-line browser</a>',
'hundesuche\.com\-bot','<a href="http://www.hundesuche.com" title="Hundesuche.com-Bot home page">Hundesuche.com-Bot</a>',
'ichiro','<a href="http://help.goo.ne.jp/door/crawlerE.html" title="Bot home page">ichiro</a>',
'iltrovatore\-setaccio','<a href="http://www.iltrovatore.it/aiuto/motore_di_ricerca.html" title="bot@iltrovatore.it IlTrovatore-Setaccio home page">IlTrovatore-Setaccio</a>',
'infobot','<a href="http://www.infobot.org" title="InfoBot home page">InfoBot</a>',
'infociousbot','<a href="http://corp.infocious.com/tech_crawler.php" title="InfociousBot home page">InfociousBot</a>',
'infomine','<a href="http://infomine.ucr.edu/useragents/" title="Bot home page">INFOMINE VLCrawler</a>',
'insurancobot','<a href="http://www.fastspywareremoval.com" title="InsurancoBot home page">InsurancoBot</a>',
'internet\_ninja','<a href="http://www.dti.ne.jp" title="Internet_Ninja home page">Internet_Ninja </a>',
'internetarchive','<a href="http://lucene.apache.org/nutch/bot.html " title="InternetArchive home page">InternetArchive</a>',
'internetseer', 'InternetSeer',
'internetsupervision','<a href="http://internetsupervision.com" title="InternetSupervision home page">InternetSupervision</a>',
'irlbot','<a href="http://irl.cs.tamu.edu/crawler" title="Bot home page">IRLbot</a>',
'isearch2006','<a href="http://www.yahoo.com.cn" title="isearch2006 home page">isearch2006</a>',
'iupui_research_bot','<a href="http://spamhuntress.com/2005/04/25/a-mail-harvester-visits/" title="IUPUI_Research_Bot home page">IUPUI_Research_Bot</a>',
'jrtwine\_software\_check\_favorites\_utility','<a href="http://www.jrtwine.com/Products/CheckFavs/" title="JRTwine_Software_Check_Favorites_Utility  home page">JRTwine_Software_Check_Favorites_Utility</a>',
'justview', 'JustView',
'kalambot','<a href="http://64.124.122.251/feedback.html" title="KalamBot home page">KalamBot</a>',
'kamano\.de_newsfeedverzeichnis','<a href="http://www.kamano.de" title="kamano.de NewsFeedVerzeichnis home page">kamano.de NewsFeedVerzeichnis</a>',
'kazoombot','<a href="http://www.kazoom.ca/bot.html" title="kazoombot@kazoom.ca KazoomBot home page">KazoomBot</a>',
'kevin','<a href="http://dznet.com/kevin/" title="Kevin home page">Kevin</a>',
'keyoshid','<a href="http://www.yahoo.co.jp" title="Bot home page">Yahoo! Japan keyoshid robot study</a>',
'kinjabot', 'Kinjabot',
'kinja\-imagebot', 'Kinja Imagebot',
'knowitall','<a href="http://www.cs.washington.edu/research/knowitall/" title="KnowItAll home page">KnowItAll</a>',
'knowledge\.com','<a href="http://www.knowledge.com" title="Knowledge.com home page">Knowledge.com</a>',
'kouaa_krawler','<a href="http://www.kouaa.com" title="Kouaa Krawler home page">Kouaa Krawler</a>',
'krugle','<a href="http://www.krugle.com/crawler/info.html" title="Bot home page">Krugle</a>',
'ksibot','<a href="http://ego.ms.mff.cuni.cz" title="Bot home page">ksibot</a>',
'kurzor','<a href="http://www.easymail.hu" title="cursor@easymail.hu Kurzor home page">Kurzor</a>',
'lanshanbot','<a href="http://www.psychedelix.com/cgi-bin/csv2html.pl?data=allagents.csv&amp;template=detail.html&amp;match=%5Cbid_g_l_140406_1%5Cb" title="Bot Information">lanshanbot</a>',
'letscrawl\.com','<a href="http://letscrawl.com" title="Bot home page">LetsCrawl.com</a>',
'libcrawl','Crawl libcrawl',
'link_valet_online','<a href="http://www.htmlhelp.com/tools/valet/" title="Link Valet Online home page">Link Valet Online</a>',
'linkbot','LinkBot',
'linkchecker','<a href="http://linkchecker.sourceforge.net" title="Bot home page">LinkChecker</a>',
'livejournal\.com', 'LiveJournal.com',
'magpierss', 'MagpieRSS',
'mail\.ru', 'Mail.ru bot',
'mapoftheinternet\.com','<a href="http://MapoftheInternet.com" title="MapoftheInternet.com home page">MapoftheInternet.com</a>',
'mediapartners\-google','<a href="https://adwords.google.com" title="Bot home page">Google AdSense</a>',
'megite','<a href="http://www.megite.com" title="Megite home page">Megite</a>',
'metager\-linkchecker','MetaGer LinkChecker',
'metaspinner','<a href="http://index.meta-spinner.de" title="Metaspinner home page">Metaspinner</a>',
'microsoft\surl\scontrol','<a href="http://www.webmasterworld.com/forum11/1005.htm" title="Microsoft URL Control  home page">Microsoft URL Control</a>',
'minirank','<a href="http://minirank.com" title="miniRank home page">miniRank</a>',
'mini\-reptile','Mini-reptile',
'missigua_locator','<a href="http://www.webmasterworld.com/forum11/2690.htm" title="Missigua_Locator  home page">Missigua_Locator</a>',
'misterbot','<a href="http://www.misterbot.fr" title="Misterbot home page">Misterbot</a>',
'miva','<a href="http://www.miva.com" title="Miva home page">Miva</a>',
'mizzu_labs','<a href="http://www.psychedelix.com/cgi-bin/csv2html.pl?data=allagents.csv&amp;template=detail.html&amp;match=\bid_m_141105_2\b " title="Mizzu Labs home page">Mizzu Labs</a>',
'mj12bot','<a href="http://majestic12.co.uk/bot.php" title="Bot home page.">MJ12bot</a>',
'mojeekbot','<a href="http://www.mojeek.com/bot.html" title="Bot home page.">MojeekBot</a>',
'msiecrawler','<a href="http://msdn.microsoft.com/workshop/delivery/offline/linkrel.asp" title="Bot home page.">MSIECrawler</a>',
'ms\ssearch\s4\.0\srobot','<a href="http://support.microsoft.com/default.aspx?scid=kb;en-us;284022" title="Bot home page.">MS SharePoint Portal Server - MS Search 4.0 Robot</a>',
'msrabot','msrabot',
'msrbot','<a href="http://research.microsoft.com/research/sv/msrbot/" title="MSRBOT home page">MSRBOT</a>',
'mt::telegraph::agent','MT::Telegraph::Agent',
'mydoyouhike','<a href="http://www.doyouhike.net/my" title="Mydoyouhike home page">Mydoyouhike</a>',
'nagios','Nagios',
'nasa_search','<a href="http://www.psychedelix.com/cgi-bin/csv2html.pl?data=allagents.csv&amp;template=detail.html&amp;match=\bid_n_s_140506_2\b" title="NASA Search home page">NASA Search</a>',
'netluchs','<a href="http://www.netluchs.de" title="Bot home page.">Netluchs</a>',
'netsprint','<a href="http://www.netsprint.pl/serwis/" title="NetSprint home page">NetSprint</a>',
'newsgatoronline', 'NewsGator Online',
'nicebot','<a href="http://www.egghelp.org/setup.htm" title="Bot home page (there may be others)">nicebot</a>',
'nimblecrawler','<a href="http://www.healthline.com" title="NimbleCrawler home page">NimbleCrawler</a>',
'noxtrumbot','<a href="http://www.noxtrum.com" title="Bot home page">noxtrumbot</a>',
'npbot','<a href="http://www.nameprotect.com/botinfo.html" title="NPBot home page">NPBot</a>',
'nutchcvs','<a href="http://lucene.apache.org/nutch/bot.html" title="NutchCVS home page">NutchCVS</a>',
'nutchosu\-vlib','<a href="http://lucene.apache.org/nutch/bot.html" title="NutchOSU-VLIB home page">NutchOSU-VLIB</a>',
'nutch','<a href="http://lucene.apache.org/nutch/" title="Bot home page. Used by many, including Looksmart.">Nutch</a>',
'ocelli','<a href="http://www.globalspec.com/Ocelli/" title="Ocelli home page">Ocelli</a>',
'octora_beta_bot','<a href="http://www.octora.com" title="Bot home page">Octora Beta Bot</a>',
'omniexplorer\_bot','<a href="http://www.omni-explorer.com" title="Bot home page.">OmniExplorer Bot</a>',
'onet\.pl\_sa','<a href="http://szukaj.onet.pl" title="Onet.pl_SA home page">Onet.pl_SA</a>',
'onfolio','<a href="http://www.onfolio.com" title="Bot home page">Onfolio</a>',
'opentaggerbot','<a href="http://www.opentagger.com/opentaggerbot.htm" title="Bot home page">OpenTaggerBot</a>',
'openwebspider','<a href="http://www.openwebspider.org" title="OpenWebSpider home page">OpenWebSpider</a>',
'oracle_ultra_search','<a href="http://www.oracle.com/technology/products/ultrasearch/index.html" title="Oracle Ultra Search home page">Oracle Ultra Search</a>',
'orbiter','<a href="http://www.dailyorbit.com/bot.htm" title="Orbiter home page">Orbiter</a>',
'yodaobot','<a href="http://www.yodao.com/help/webmaster/spider/" title="YodaoBot">OutfoxBot/YodaoBot</a>',
'qihoobot','<a href="http://www.qihoo.com" title="QihooBot">QihooBot</a>',
'passwordmaker\.org','<a href="http://passwordmaker.org" title="passwordmaker.org home page">passwordmaker.org</a>',
'pear_http_request_class','<a href="http://pear.php.net" title="PEAR HTTP Request class home page">PEAR HTTP Request class</a>',
'peerbot','<a href="http://www.peerbot.com" title="PEERbot home page">PEERbot</a>',
# Modified by Ryu 2006.03.15
#'perman', 'Perman surfer',
'perman', '<a href="http://www.bug.co.jp/nami-nori/" title="Bot home page.">波乗野郎</a>',
'php\sversion\stracker','<a href="http://www.nexen.net/phpversion/bot.php" title="PHP version tracker home page">PHP version tracker</a>',
'pictureofinternet','<a href="http://malfunction.org/poi/" title="PictureOfInternet home page">PictureOfInternet</a>',
'ping\.blo\.gs','<a href="http://blo.gs/ping.php" title="Bot home page.">ping.blo.gs</a>',
'plinki','<a href="http://www.plinki.com" title="plinki home page">plinki</a>',
'pluckfeedcrawler','<a href="http://www.pluck.com" title="Bot home page.">PluckFeedCrawler</a>',
'pogodak','<a href="http://www.pogodak.com" title="Pogodak home page">Pogodak.com</a>',
'pompos','<a href="http://dir.com/pompos.html" title="Bot home page.">Pompos</a>',
'popdexter','Popdexter',
'port_huron_labs','<a href="http://www.psychedelix.com/cgi-bin/csv2html.pl?data=allagents.csv&amp;template=detail.html&amp;match=\bid_n_s_1133\b" title="Port Huron Labs home page">Port Huron Labs</a>',
'postfavorites','<a href="http://www.psychedelix.com/cgi-bin/csv2html.pl?data=allagents.csv&amp;template=detail.html&amp;match=\bid_n_s_1135\b " title="PostFavorites home page">PostFavorites</a>',
'projectwf\-java\-test\-crawler','ProjectWF-java-test-crawler',
'proodlebot','<a href="http://www.proodle.com" title="proodleBot home page">proodleBot</a>',
'pyquery','<a href="http://sourceforge.net/projects/pyquery/" title="PyQuery home page">PyQuery</a>',
'rambler','<a href="http://www.rambler.ru/doc/faq.shtml" title="Bot home page">StackRambler</a>',
'redalert','Red Alert',
'relevantnoise\.com', '<a href="http://www.relevantnoise.com" title="Relevant Noise">Relevant Noise</a>',
'rojo','<a href="http://rojo.com" title="Bot home page">RoJo</a> aggregator',
'rssimagesbot','<a href="http://herbert.groot.jebbink.nl/?app=rssImages" title="Bot home page">rssImagesBot</a>',
'ruffle','<a href="http://www.unreach.net" title="Bot home page">ruffle SemanticWeb crawler</a>',
'rufusbot','<a href="http://64.124.122.252.webaroo.com/feedback.html" title="Bot home page">RufusBot Rufus Web Miner</a>',
'sandcrawler','<a href="http://www.microsoft.com" title="Bot home page">SandCrawler (Microsoft)</a>',
'sbider','<a href="http://www.sitesell.com/sbider.html" title="Bot home page">SBIder</a>',
'schizozilla','<a href="http://spamhuntress.com/2005/03/18/gizmo/ " title="Schizozilla home page">Schizozilla</a>',
'scumbot','Scumbot',
'searchguild\_dmoz\_experiment','<a href="http://www.searchguild.com" title="SearchGuild_DMOZ_Experiment  home page">SearchGuild_DMOZ_Experiment</a>',
'seekbot','<a href="http://www.seekbot.net/bot.html" title="Bot home page">Seekbot</a>',
'sensis\sweb\scrawler','<a href="http://www.sensis.com.au" title="Sensis Web Crawler home page">Sensis Web Crawler</a>',
'seznambot','<a href="http://fulltext.seznam.cz" title="Bot home page">SeznamBot</a>',
'shim\-crawler','<a href="http://www.logos.ic.i.u-tokyo.ac.jp/crawler/" title="crawl@logos.ic.i.u-tokyo.ac.jp Bot home page">Shim-Crawler</a>',
'shoutcast','Shoutcast Directory Service',
'slysearch','SlySearch',
'snap\.com_beta_crawler','<a href="http://www.snap.com" title="snap.com beta crawler home page">snap.com beta crawler</a>',
'sohu\-search','<a href="http://corp.sohu.com" title="Bot home page">sohu-search</a>',
'sohu','<a href="http://corp.sohu.com" title="Bot home page">sohu agent</a>',
'snappy','<a href="http://www.urltrends.com/faq.php" title="Bot home page">Snappy</a>',
'sphere\sscout','<a href="http://www.sphere.com" title="Bot home page">Sphere Scout</a>',
'spip','<a href="http://www.spip.net" title="SPIP home page">SPIP</a>',
'sproose_crawler','<a href="http://www.sproose.com/bot.html" title="Bot home page">sproose crawler</a>',
'steroid__download','<a href="http://faqs.org.ru/progr/pascal/delphi_internet2.htm" title="STEROID  Download home page">STEROID  Download</a>',
'steeler','<a href="http://www.tkl.iis.u-tokyo.ac.jp/~crawler/" title="Steeler home page">Steeler</a>',
'suchfin\-bot','<a href="http://www.suchfin.de" title="Suchfin-Bot home page">Suchfin-Bot</a>',
'superbot','<a href="http://www.sparkleware.com/superbot/" title="SuperBot home page">SuperBot</a>',
'surveybot','SurveyBot',
'susie','<a href="http://www.sync2it.com/bms/susie.php" title="Susie home page">Susie</a>',
'syndic8','Syndic8',
'syndicapi','<a href="http://syndicapi.com/bot.html" title="Bot home page">SyndicAPI</a>',
'synoobot','<a href="http://www.synoo.de/bot.html" title="webmaster@synoo.com SynooBot home page">SynooBot</a>',
'tcl_http_client_package','<a href="http://www.tcl.tk/man/tcl8.4/TclCmd/http.htm" title="Tcl http client package home page">Tcl http client package</a>',
'technoratibot', 'Technoratibot',
'teragramcrawlersurf','<a href="http://www.teragram.com" title="TeragramCrawlerSURF home page">TeragramCrawlerSURF</a>',
'test\scrawler','<a href="http://netp.ath.cx" title="Test Crawler home page">Test Crawler</a>',
'testbot','<a href="http://www.agbrain.com" title="TestBot home page">TestBot</a>',
't\-h\-u\-n\-d\-e\-r\-s\-t\-o\-n\-e','<a href="http://www.thunderstone.com" title="Bot home page. Used by many.">T-H-U-N-D-E-R-S-T-O-N-E</a>',
'topicblogs', '<a href="http://www.topicblogs.com" title="Bot home page">topicblogs</a>',
'turnitinbot','<a href="http://www.turnitin.com/robot/crawlerinfo.html">TurnitinBot</a>',
'turtle', 'Turtle',
'turtlescanner', 'Turtle',
'tutorgigbot','<a href="http://www.tutorgig.info" title="TutorGigBot home page">TutorGigBot</a>',
'twiceler','<a href="http://www.cuill.com/twiceler/robot.html" title="Twiceler home page">twiceler</a>',
'ubicrawler','<a href="http://law.dsi.unimi.it/ubicrawler/" title="Bot home page">UbiCrawler</a>',
'ultraseek', 'Ultraseek',
'unchaos_bot_hybrid_web_search_engine','<a href="http://www.unchaos.com" title="UnChaos Bot Hybrid Web Search Engine home page">UnChaos Bot Hybrid Web Search Engine</a>',
'unido\-bot','<a href="http://www.unchina.org/unido/unido/our_projects/3_3.html" title="unido-bot home page">unido-bot</a>',
'updated','<a href="http://www.updated.com" title="updated home page">updated</a>',
'ustc\-semantic\-group','<a href="http://ai.ustc.edu.cn/mas/en/research/index.php" title="Bot home page">USTC-Semantic-Group</a>',
'vagabondo\-wap','<a href="http://www.wise-guys.nl/Contact/index.php?botselected=webagents&amp;lang=uk" title="Bot home page">Vagabondo-WAP</a>',
'vagabondo','<a href="http://www.wise-guys.nl/Contact/index.php?botselected=webagents&amp;lang=uk" title="Bot home page">Vagabondo</a>',
'vermut','<a href="http://vermut.aol.com" title="Bot home page">Vermut</a>',
'versus_crawler_from_eda\.baykan@epfl\.ch','<a href="http://www.epfl.ch/Eindex.html  " title="versus crawler from eda.baykan@epfl.ch home page">versus crawler from eda.baykan@epfl.ch</a>',
'vespa_crawler','<a href="http://www.psychedelix.com/cgi-bin/csv2html.pl?data=allagents.csv&template=detail.html&match=%5Cbid_t_z_030406_1%5Cb" title="Bot home page">Vespa Crawler</a>',
'vortex','<a href="http://marty.anstey.ca/projects/robots/vortex/" title="Bot home page">VORTEX</a>',
'vse\/','<a href="http://www.vivisimo.com" title="VSE home page">VSE</a>',
'w3c\-checklink','<a href="http://validator.w3.org/checklink/" title="Bot home page">W3C Link Checker</a>',
'w3c\_css\_validator\_jfouffa', '<a href="http://jigsaw.w3.org/css-validator/" title="Bot home page">W3C jigsaw CSS Validator</a>',
'w3c_validator','<a href="http://validator.w3.org" title="Bot home page">W3C Validator</a>',
'wavefire','<a href="http://www.wavefire.com" title="info@wavefire.com; Bot home page">Wavefire</a>',
'webclipping\.com', 'WebClipping.com',
'webcompass', 'webcompass',
'webcrawl\.net','<a href="http://www.webcrawl.net" title="webcrawl.net home page">webcrawl.net</a>',
'web_downloader','<a href="http://www.krasu.ru/soft/chuchelo/" title="Web Downloader home page">Web Downloader</a>',
'webdup','<a href="http://www.webdup.com/en/index.html" title="Webdup home page">Webdup</a>',
'webfilter','<a href="http://www.verso.com/enterprise/netspective/webfilter.asp" title="Bot home page">WebFilter</a>',
'webindexer','<a href="mailto://webindexerv1@yahoo.com" title="WebIndexer home page">WebIndexer</a>',
'webminer','<a href="http://64.124.122.252/feedback.html" title="WebMiner home page">WebMiner</a>',
'website\_monitoring\_bot','<a href="http://InternetSupervision.com/UrlMonitor/3/" title="Website_Monitoring_Bot home page">Website_Monitoring_Bot</a>',
'webvulncrawl', 'WebVulnCrawl',
'wells_search','<a href="http://www.psychedelix.com/cgi-bin/csv2html.pl?data=allagents.csv&amp;template=detail.html&amp;match=\bid_t_z_1484\b " title="Wells Search home page">Wells Search</a>',
'wonderer', 'Web Wombat Redback Spider',
'wume_crawler','<a href="http://wume.cse.lehigh.edu/~xiq204/crawler/ " title="wume crawler home page">wume crawler</a>',
'wwweasel',,'<a href="http://wwweasel.de" title="Website_Monitoring_Bot home page">WWWeasel</a>',
'xenu\'s_link_sleuth','<a href="http://home.snafu.de/tilman/xenulink.html" title="Xenu Link Sleuth home page">Xenu Link Sleuth</a>',
'xenu\slink\ssleuth','<a href="http://home.snafu.de/tilman/xenulink.html" title="Xenu Link Sleuth home page">Xenu Link Sleuth</a>',
'xirq','<a href="http://www.xirq.com" title="xirq home page">xirq</a>',
'y!j', '<a href="http://help.yahoo.co.jp/help/jp/search/indexing/indexing-15.html" title="Bot home page">Y!J Yahoo Japan</a>',
'yacy','<a href="http://www.yacy.net/yacy" title="Bot home page">yacy</a>',
'yahoo\-blogs','<a href="http://help.yahoo.com/help/us/ysearch/crawling/crawling-02.html" title="Bot home page">Yahoo-Blogs</a>',
'yahoo\-verticalcrawler', 'Yahoo Vertical Crawler',
'yahoofeedseeker', '<a href="http://publisher.yahoo.com/rssguide" title="Bot home page">Yahoo Feed Seeker</a>',
'yahooseeker\-testing', '<a href="http://search.yahoo.com" title="Bot home page">YahooSeeker-Testing</a>',
'yahooseeker', '<a href="http://help.yahoo.com/help/us/ysearch/crawling/crawling-02.html" title="Bot home page">YahooSeeker Yahoo! Blog crawler</a>',
'yahoo\-mmcrawler', '<a href="mailto:mms-mmcrawler-support@yahoo-inc.com?subject=Yahoo-MMCrawler_Information" title="E-mail Bot">Yahoo-MMCrawler</a>',
'yahoo!_mindset','<a href="http://mindset.research.yahoo.com" title="Bot home page">Yahoo! Mindset</a>',
'yandex', 'Yandex bot',
'flexum', 'Flexum Search Engine',
'yanga', 'Yanga WorldSearch Bot',
'yooglifetchagent','<a href="http://www.yoogli.com" title="yoogliFetchAgent home page">yoogliFetchAgent</a>',
'z\-add_link_checker','<a href="http://w3.z-add.co.uk/linkcheck/" title="Z-Add Link Checker home page">Z-Add Link Checker</a>',
'zealbot','ZealBot',
'zhuaxia','<a href="http://www.zhuaxia.com">ZhuaXia</a>',
'zspider','<a href="http://feedback.redkolibri.com" title="Bot home page">zspider</a>',
'zeus','<a href="http://www.webmasterworld.com/forum11/1840.htm" title="Bot documentation">Zeus Webster Pro</a>',
'ng\/1\.','<a href="http://www.exabot.com" title="Bot home page">NG 1.x (Exalead)</a>', # put at end to avoid false positive
'ng\/2\.','<a href="http://www.exabot.com" title="Bot home page">NG 2.x (Exalead)</a>', # put at end to avoid false positive
'exabot','<a href="http://www.exabot.com" title="Bot home page">Exabot</a>', # put at end to avoid false positive
# Other id that are 99% of robots
'wget','WGet tools',
'libwww\-perl','Perl tool',
'w3c\-webcon','WebCon - the Libwww Command Line Tool',
'lwp','LibWWW-perl',
'java\/[0-9]',"<a href=\"http://www.projecthoneypot.org/harvester_useragents.php\" title=\"Bot home page\">Java ($Message[211])</a>", # put at end to avoid false positive
# Generic robot
'robot', "$Message[204]",
'checker', "$Message[216]",
'crawl', "$Message[205]",
'discovery', "$Message[217]",
'hunter', "$Message[218]",
'scanner', "$Message[219]",
'spider', "$Message[206]",
'sucker', "$Message[220]",
'bot[\s_+:,\.\;\/\\\-]',"$Message[207]",
'[\s_+:,\.\;\/\\\-]bot',"$Message[208]",
'no_user_agent',"$Message[209]",
'uri::fetch','URI::Fetch',
# Unknown robots identified by hit on robots.txt
'unknown', "$Message[210]"
);


# RobotsAffiliateLib
# This list try to tell by which Search Engine a robot is used
#-------------------------------------------------------------
%RobotsAffiliateLib = (
'fast\-webcrawler'=>'AllTheWeb',
'googlebot'=>'Google',
'google\-sitemap'=>'Google',
'msnbot'=>'MSN',
'nutch'=>'Looksmart',
'scooter'=>'AltaVista',
'wisenutbot'=>'Looksmart',
'yahoo\-blogs'=>'Yahoo',
'yahoo\-verticalcrawler'=>'Yahoo',
'yahoofeedseeker'=>'Yahoo',
'yahooseeker\-testing'=>'Yahoo',
'yahooseeker'=>'Yahoo',
'yahoo\-mmcrawler'=>'Yahoo',
'yahoo!_mindset'=>'Yahoo',
'zyborg'=>'Looksmart',
'cfetch'=>'Kosmix',
'^voyager\/'=>'Kosmix'
);

1;
