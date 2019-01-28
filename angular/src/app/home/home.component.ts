import {ChangeDetectionStrategy, Component} from '@angular/core';
import {FeedService} from '@app/core/feed.service';
import {share} from 'rxjs/operators';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss'],
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class HomeComponent {

  feed$ = this.service.feed().pipe(share());

  constructor(private service: FeedService) {

  }
}
