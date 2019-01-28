import {Component} from '@angular/core';
import {environment} from '@env/environment';
import {AbstractControl, AsyncValidatorFn, FormBuilder, FormGroup, ValidatorFn, Validators} from '@angular/forms';
import {Router} from '@angular/router';
import {AuthenticationService, Logger} from '@app/core';
import {debounceTime, distinctUntilChanged, finalize, map} from 'rxjs/operators';

const log = new Logger('Register');

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.scss']
})
export class RegisterComponent {
  version: string = environment.version;
  error: string;
  registerForm: FormGroup;
  isLoading = false;

  constructor(private router: Router,
              private formBuilder: FormBuilder,
              private authenticationService: AuthenticationService) {
    this.createForm();
  }

  register() {
    this.isLoading = true;
    const {email, passwords: {first: password}} = this.registerForm.value;
    this.authenticationService.register({email, password})
      .pipe(finalize(() => {
        this.isLoading = false;
        this.registerForm.markAsPristine();
      }))
      .subscribe(success => {
        log.debug(`successfully registered`);
        if (success) {
          this.registerForm.reset();
          this.router.navigate(['/login'], { replaceUrl: true, queryParams: {confirm: true} });
        }
      });
  }

  private createForm() {
    const equalValidator: ValidatorFn = (group: FormGroup) => {
      return group.controls.first.value === group.controls.second.value ? null : {notSame: true};
    };

    const passFieldValidators = [Validators.required, Validators.minLength];
    const uniqueMailValidator: AsyncValidatorFn = (c: AbstractControl) => {
      return this.authenticationService.validate(c.value)
        .pipe(
          distinctUntilChanged(),
          debounceTime(500),
          map(valid => valid ? null : {nonUniqMail: true})
        );
    };

    this.registerForm = this.formBuilder.group({
      email: ['', [Validators.required, Validators.email], uniqueMailValidator],
      passwords: this.formBuilder.group({
        first: [null, passFieldValidators],
        second: [null, passFieldValidators]
      }, {
        validators: [
          equalValidator
        ]
      }),
    });
  }

}
