@extends('tenant.template.base')
@section('content')
<style>
    .timeline-list .timeline-item {
        border-bottom: 1px solid #e5e5e5;
        padding-bottom: 25px;
        margin-bottom: 25px;
    }

    .timeline-list .timeline-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
</style>
	<div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">News Feed</h3>
                </div><!-- .nk-block-head-content -->
            </div><!-- .nk-block-between -->
        </div><!-- .nk-block-head -->
        <div class="nk-block">
            <div class="card card-preview">
                <div class="card-inner">
                    <div class="card-title-group">
                        <div class="card-title">
                            <h6 class="title">
                                <span class="mr-2">News Feed</span>
                            </h6>
                        </div>
                    </div>
                    <br/>
                    <?php
                        if (!empty($datanewsfeed))
                        {
                            $iconic = array(
                                'bg-success',
                                'bg-primary',
                                'bg-warning',
                                'bg-danger'
                            );
                            $bulan = array(
                                1 => 'Januari',
                                'Februari',
                                'Maret',
                                'April',
                                'Mei',
                                'Juni',
                                'Juli',
                                'Agustus',
                                'September',
                                'Oktober',
                                'November',
                                'Desember'
                            );                        
                    ?>
                	<ul class="timeline-list">
                        <?php
                            foreach ($datanewsfeed as $newsfeed)
                            {

                            	echo '<li class="timeline-item" id="news-'.$newsfeed->id.'">';
                            	echo '	<div class="timeline-status '.$iconic[$newsfeed->status].' is-outline"></div>';
                            	echo '	<div class="timeline-data">
	                            			<h6 class="timeline-title">'.$newsfeed->subject.'</h6>
	                            			<div class="timeline-des">';
                                                if (!empty($newsfeed->picture))
                                                {
	                            				   echo '
                                <div style="
                                    display:inline-block;
                                    background:#111;
                                    padding:1px;
                                    border-radius:2px;
                                ">
                                    <img src="'.$newsfeed->picture.'" alt="" class="img-responsive">
                                </div>';
                                                }                                                   
                                                if (!empty($newsfeed->youtube_link)) {
                                                // Extract video ID from the YouTube URL
                                                    $video_id = '';
                                                    parse_str(parse_url($newsfeed->youtube_link, PHP_URL_QUERY), $video_id);
                                                    
                                                    // Construct the embed URL
                                                    $embed_url = 'https://www.youtube.com/embed/' . $video_id['v'];

                                                    // Output the iframe with the embed URL
                                                    echo '<div class="embed-responsive-16by9">
                                                            <iframe width="640" height="360" src="'.$embed_url.'"></iframe>
                                                          </div>';
                                                }
                                        echo ''.$newsfeed->content.'';
										echo '</div>';
								echo '	</div>';
                            	echo '</li>';
                            }
                        ?>
                	</ul>
                    <?php
                        } else {
                    ?>
                    <p class='card-text badge badge-gray'>No Latest News</p>
                    <?php
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
@endsection