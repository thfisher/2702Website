<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="initial-scale=1.0">
  <title>blog</title>
  <link href="//fonts.googleapis.com/css?family=Ubuntu:700,300" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="css/standardize.css">
  <link rel="stylesheet" href="css/blog-grid.css">
  <link rel="stylesheet" href="css/blog.css">
</head>
<body class="body page-blog clearfix">

<?php
include( "config.php" );

try 
{
	mysqli_report(MYSQLI_REPORT_STRICT);
	require_once("nbbc-1.4.5/src/nbbc_main.php");

	// Forum ID to read blog messages from...
	$forum_id=5;


	$db = new mysqli( $dbhost, $dbuser, $dbpass, $dbname );
	if( $db->connect_error > 0 )
	{
		throw new Exception('Unable to connect to the database');
	}

	$bbcode = new BBCode;
	$bbcode->SetAllowAmpersand( truei);
//  enable to get debugging of BBCode parser
//	$bbcode->SetDebug( true );

	// Handle a commented block.  Look for BBCode and smilies...
	function HandleCommented( $txt )
	{
		$out = "";
		$outidx = 0;

		$txt = htmlspecialchars_decode( $txt );

		$txtlen = strlen($txt);
		$idx = 0;

		do {
			if( $idx < ($txtlen - 4) && $txt[$idx] == '<' && $txt[$idx + 1] == '!' && $txt[$idx+2] == '-' && $txt[$idx+3] == '-' )
			{
				$comment = "";
				$last = 'n';

				while( $last != '>' && $idx < $txtlen )
				{
					$last = $txt[$idx++];
					$comment .= $last;
				}

				if( $comment[5] == 's' )
				{
					$smtxt = "";
					for( $i = 6; $comment[$i] != ' '; $i++ )
					{
						$smtxt .= $comment[$i];
					}
					$offset = strpos( $txt, $comment, $idx );
					if( $offset )
					{
						$smiley = substr( $txt, $idx, $offset - $idx - 1 );
						$ret = preg_match( "/icon[^\" ]+gif/", $smiley, $matches );
						if( $ret )
						{
							$tag = sprintf( "<img src=\"smilies/%s\" alt=\"%s\" title=\"%s\" class=\"bbcode_smiley\" />", $matches[0], $smtxt, $smtxt );
						}
						else
						{
							$tag = sprintf( "%s", $smtxt );
						}
						$idx = $offset;
						$out .= $tag;
					}
				}

			}
			else
			{
				$out .= $txt[$idx++];
			}
		} while( $idx < $txtlen );

		return $out;
	}

	// Load the posts from the specified forum id.
	function LoadPosts( $forum )
	{
		global $db;
		global $bbcode;

		// First get the list of topics in this forum
		$sql = sprintf( "select topic_id,topic_time from phpbb_topics where forum_id=%d order by topic_time DESC", $forum );
		$res = $db->query( $sql );

		// Now that we have the list of topics from the database
		// run through the list of topics
		while( $myrow = $res->fetch_assoc() )
		{
			// Query the database for the list of posts for the current topic
			$sql = sprintf( "select post_id,post_subject,post_time,post_text from phpbb_posts where topic_id=%d order by post_time ASC", $myrow['topic_id'] );
			$nres = $db->query( $sql );
//			printf( "Returned %d posts<br>\n", $nres->num_rows();

			// We have a list of topics, run through each one.
			while( $topicrow = $nres->fetch_assoc() )
			{
				// print the topic's subject an date
				printf( "<b>%s</b><br>%s<br><br>\n", $topicrow['post_subject'], date( "Y-M-d", $topicrow['post_time'] ) );

				// Parse any BBCode that may be in the post
				$decoded = $bbcode->Parse( $topicrow['post_text'] );

				// Look for smilies in the post
				$cleaned = HandleCommented( $decoded );

				// print the resulting decoded,cleaned post bocy
				print $cleaned;

				printf( "<br><br>\n" );
				}
			}
			$nres->free();
			printf( "<hr>\n" );
		}
		$res->free();
	}

	LoadPosts( $forum_id );
}

catch( Exception $e )
{
	printf( "%s\n", $e->getMessage() );
}

?>

  <script src="js/jquery-min.js"></script>
</body>
</html>
