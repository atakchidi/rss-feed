import {Injectable} from '@angular/core';
import {of} from 'rxjs';
import {HttpClient} from '@angular/common/http';
import {catchError, pluck, tap, map} from 'rxjs/operators';

export interface LoginContext {
  email: string;
  password: string;
  remember?: boolean;
}

const credentialsKey = 'token';

@Injectable()
export class AuthenticationService {

  private _token: string;

  constructor(private http: HttpClient) {
    this._token = sessionStorage.getItem(credentialsKey) || localStorage.getItem(credentialsKey);
  }

  /**
   * Authenticates the user.
   * @param context The auth parameters.
   * @return The user token.
   */
  login(context: LoginContext) {
    const {email: username, password} = context;
    const data = {username, password};

    return this.http.post('/login', data)
      .pipe(
        pluck('token'),
        tap((token: string) => this.setCredentials(token, context.remember))
      );
  }

  register(context: LoginContext) {
    return this.http.post('/register', context)
      .pipe(
        map(() => true),
        catchError(() => of(false))
      );
  }

  validate(email: string) {
    return this.http.post('/validate/mail', {email})
      .pipe(
        map(() => true),
        catchError(() => of(false))
      );
  }

  logout() {
    this.setCredentials();
  }

  isAuthenticated(): boolean {
    return !!this.token;
  }

  get token() {
    return this._token;
  }

  private setCredentials(token?: string, remember?: boolean) {
    this._token = token;

    if (token) {
      const storage = remember ? localStorage : sessionStorage;
      storage.setItem(credentialsKey, token);
    } else {
      sessionStorage.removeItem(credentialsKey);
      localStorage.removeItem(credentialsKey);
    }
  }

}
