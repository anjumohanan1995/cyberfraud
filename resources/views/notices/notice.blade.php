
<div class="container-fluid">


                    @foreach ($notices as $notice)


                    <div class="container-fluid">
                        <div class="notice-header">
                            <b>{{ $notice['sub'] }}</b>
                        </div>
                        <div class="section">
                            <p>{!! preg_replace('/\((.*?)\)/', '(<strong>$1</strong>)', $notice['main_content']) !!}</p>
                            <p>{{ $notice['content_1'] }}</p>
                            <p>{{ $notice['content_2'] }}</p>
                        </div>
                        <div class="section">
                            <div class="section-title"><b>{{ $notice['url_head'] }}</b></div>
                            <table class="details-table">
                                <tr>
                                    <th>Website URL:</th>
                                    <td><b>{{ $notice['url'] }}</b></td>
                                </tr>
                                <tr>
                                    <th>Domain Name:</th>
                                    <td><b>{{ $notice['domain_name'] }}</b></td>
                                </tr>
                                <tr>
                                    <th>Registry Domain ID:</th>
                                    <td><b>{{ $notice['domain_id'] }}</b></td>
                                </tr>
                            </table>
                        </div>
                        <div class="section">
                            <div class="section-title"><b>{{ $notice['details_head'] }}</b></div>
                            <ol>
                                {!! nl2br(e($notice['details_content'])) !!}
                            </ol>
                        </div>


                        <div class="footer">
                            <p>{{ $notice['footer_content'] }}</p>
                            <a href="mailto:cyberops-fsm.pol@kerala.gov.in">cyberops-fsm.pol@kerala.gov.in</a><br>
                            {{-- <a href="{{ route('notices.index') }}" class="btn btn-secondary">Back to List</a> --}}
                            {{-- <a href="{{ route('notices.edit', $notice->id) }}" class="btn btn-success">Update</a> --}}
                        </div>
                    </div>
                    @endforeach

    <!-- /row -->
</div>


