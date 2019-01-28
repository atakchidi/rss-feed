import {Injectable} from '@angular/core';
import {HttpClient} from '@angular/common/http';

export interface Feed {
  title: string;
  link: string;
  description: string;
}

@Injectable({
  providedIn: 'root'
})
export class FeedService {

  constructor(private http: HttpClient) {

  }

  feed() {
    return this.http.get<Feed[]>('/feed');
  }
}
