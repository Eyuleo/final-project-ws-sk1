<!--
Sync Impact Report:
- Version change: Initial → 1.0.0
- New constitution created with 7 core principles
- Added sections: Code Quality Standards, User Experience Standards, Performance Requirements
- Templates requiring updates:
  ✅ constitution.md - Created
  ⚠ plan-template.md - Needs Constitution Check section update
  ⚠ spec-template.md - Already aligned with testable requirements
  ⚠ tasks-template.md - Already aligned with testing discipline
- Follow-up TODOs: None
-->

# Laravel Application Constitution

## Core Principles

### I. Code Quality & Maintainability (NON-NEGOTIABLE)

**All code MUST adhere to the following standards:**

- **PSR-12 Compliance**: All PHP code MUST follow PSR-12 coding standards enforced by Laravel Pint
- **Type Safety**: All methods MUST declare parameter types and return types; use strict types (`declare(strict_types=1)`)
- **Single Responsibility**: Each class MUST have one clear purpose; methods MUST do one thing well
- **DRY Principle**: Code duplication MUST be eliminated through proper abstraction
- **Meaningful Names**: Variables, methods, and classes MUST have descriptive, intention-revealing names
- **Documentation**: Public APIs MUST have PHPDoc blocks; complex logic MUST include inline comments explaining "why", not "what"

**Rationale**: Maintainable code reduces technical debt, accelerates feature development, and minimizes bugs. Type safety catches errors at development time rather than production.

### II. Test-First Development (NON-NEGOTIABLE)

**Testing discipline MUST be followed:**

- **TDD Cycle**: Write failing test → Implement minimum code to pass → Refactor → Repeat
- **Test Coverage**: All business logic MUST have unit tests; critical user journeys MUST have feature tests
- **Test Types Required**:
  - **Unit Tests**: For models, services, helpers, and business logic (PHPUnit)
  - **Feature Tests**: For HTTP endpoints, API contracts, and user workflows (PHPUnit)
  - **Browser Tests**: For critical UI flows when JavaScript interaction is essential (Laravel Dusk - optional)
- **Test Quality**: Tests MUST be independent, repeatable, and fast; no shared state between tests
- **CI/CD Gate**: All tests MUST pass before merge; no exceptions

**Rationale**: Tests are executable specifications that prevent regressions, enable confident refactoring, and serve as living documentation.

### III. Laravel Best Practices

**Framework conventions MUST be followed:**

- **Eloquent ORM**: Use Eloquent for database operations; raw queries only when performance-critical and justified
- **Service Layer**: Complex business logic MUST reside in service classes, not controllers
- **Form Requests**: Validation MUST use Form Request classes, not inline controller validation
- **Resources**: API responses MUST use Eloquent API Resources for consistent transformation
- **Jobs & Queues**: Long-running tasks MUST be queued; synchronous processing only for <1s operations
- **Events & Listeners**: Use for decoupled side effects; avoid tight coupling in controllers
- **Middleware**: Cross-cutting concerns (auth, logging, rate limiting) MUST use middleware

**Rationale**: Laravel conventions provide proven patterns that improve code consistency, testability, and team velocity.

### IV. Database Integrity & Migrations

**Database changes MUST be managed properly:**

- **Migrations Only**: Schema changes MUST use migrations; never modify database directly
- **Rollback Safe**: Every migration MUST have a working `down()` method
- **Foreign Keys**: Relationships MUST be enforced with foreign key constraints
- **Indexes**: Queries on non-primary columns MUST have appropriate indexes
- **Seeders**: Test data MUST use factories and seeders; never commit real user data
- **Transactions**: Multi-step operations MUST use database transactions for atomicity

**Rationale**: Proper database management prevents data corruption, ensures data integrity, and enables safe deployments.

### V. Security First

**Security MUST be built-in, not bolted-on:**

- **Authentication**: Use Laravel's built-in authentication; never roll custom auth
- **Authorization**: Use policies and gates for access control; check permissions in every controller action
- **Input Validation**: ALL user input MUST be validated; use Form Requests
- **SQL Injection**: Use Eloquent or parameterized queries; never concatenate user input into SQL
- **XSS Prevention**: Use Blade templating `{{ }}` for output escaping; `{!! !!}` only for trusted content
- **CSRF Protection**: All state-changing requests MUST include CSRF token
- **Mass Assignment**: Define `$fillable` or `$guarded` on all models
- **Secrets Management**: API keys and secrets MUST be in `.env`; never committed to version control

**Rationale**: Security vulnerabilities can destroy user trust and expose the business to legal liability. Prevention is cheaper than remediation.

### VI. Performance & Scalability

**Performance MUST be considered from the start:**

- **N+1 Queries**: Use eager loading (`with()`) to prevent N+1 query problems
- **Query Optimization**: Database queries MUST complete in <100ms for p95; use `explain` to verify
- **Caching Strategy**: Expensive operations MUST be cached; use Redis for shared cache
- **Asset Optimization**: Frontend assets MUST be minified and versioned via Vite
- **Lazy Loading**: Large collections MUST use pagination or cursor pagination
- **Background Processing**: Email sending, file processing, and external API calls MUST be queued
- **Response Time**: API endpoints MUST respond in <200ms for p95; <500ms for p99
- **Memory Management**: Batch operations MUST use chunking to prevent memory exhaustion

**Rationale**: Performance directly impacts user experience and operational costs. Optimization is harder to retrofit than to build correctly.

### VII. User Experience Consistency

**User-facing features MUST provide consistent, intuitive experiences:**

- **Error Messages**: User-facing errors MUST be clear, actionable, and non-technical
- **Validation Feedback**: Form validation MUST provide inline, field-specific error messages
- **Loading States**: Async operations MUST show loading indicators; no silent failures
- **Success Confirmation**: State-changing actions MUST provide clear success feedback
- **Responsive Design**: All UI MUST work on mobile, tablet, and desktop viewports
- **Accessibility**: Forms MUST have proper labels; interactive elements MUST be keyboard-navigable
- **Consistent Patterns**: Similar actions MUST behave similarly across the application
- **Error Recovery**: Users MUST be able to recover from errors without losing data

**Rationale**: Consistent UX reduces cognitive load, increases user satisfaction, and decreases support burden.

## Code Quality Standards

### Static Analysis & Linting

- **Laravel Pint**: MUST run on all PHP files before commit; zero violations allowed
- **PHPStan**: MUST run at level 5 minimum; level 8 for critical business logic
- **IDE Integration**: Developers MUST configure IDE to show linting errors in real-time

### Code Review Requirements

- **All Changes**: Every change MUST be reviewed by at least one other developer
- **Review Checklist**:
  - Tests included and passing
  - No security vulnerabilities introduced
  - Performance implications considered
  - Documentation updated if needed
  - Follows Laravel conventions
  - No code duplication
- **Review Turnaround**: Reviews MUST be completed within 24 hours

### Technical Debt Management

- **TODO Comments**: MUST include ticket reference and explanation
- **Deprecation**: Deprecated code MUST have removal timeline and migration path
- **Refactoring**: Allocate 20% of sprint capacity to technical debt reduction

## User Experience Standards

### Frontend Technology Stack

- **Build Tool**: Vite for asset compilation and hot module replacement
- **CSS Framework**: Tailwind CSS for utility-first styling
- **JavaScript**: Modern ES6+ syntax; TypeScript for complex components
- **Components**: Reusable components for common UI patterns (buttons, forms, modals)

### UX Requirements

- **Page Load**: Initial page load MUST complete in <3s on 3G connection
- **Interactivity**: Time to Interactive (TTI) MUST be <5s
- **Visual Stability**: Cumulative Layout Shift (CLS) MUST be <0.1
- **Mobile First**: Design and develop for mobile viewport first, then scale up
- **Progressive Enhancement**: Core functionality MUST work without JavaScript

### Accessibility Standards

- **WCAG 2.1**: MUST meet Level AA compliance minimum
- **Semantic HTML**: Use proper HTML5 semantic elements
- **ARIA Labels**: Interactive elements MUST have descriptive ARIA labels
- **Keyboard Navigation**: All functionality MUST be accessible via keyboard
- **Color Contrast**: Text MUST meet 4.5:1 contrast ratio minimum

## Performance Requirements

### Application Performance

- **API Response Time**: 
  - p50: <100ms
  - p95: <200ms
  - p99: <500ms
- **Database Queries**: 
  - Individual queries: <50ms
  - Page total queries: <10 queries (use eager loading)
- **Memory Usage**: 
  - Per request: <50MB
  - Worker processes: <256MB

### Monitoring & Observability

- **Logging**: Use Laravel's logging facade; structured logs with context
- **Error Tracking**: All exceptions MUST be logged with stack traces
- **Performance Monitoring**: Track response times, query counts, and memory usage
- **Alerting**: Critical errors MUST trigger immediate alerts

### Load Testing

- **Capacity**: Application MUST handle 100 concurrent users without degradation
- **Stress Testing**: MUST be performed before major releases
- **Database Scaling**: MUST support 1M+ records with maintained performance

## Development Workflow

### Version Control

- **Branching**: Feature branches from `main`; naming: `feature/###-description`
- **Commits**: Atomic commits with descriptive messages following conventional commits
- **Pull Requests**: MUST include description, testing notes, and screenshots for UI changes

### Environment Management

- **Local Development**: Use Laravel Sail for consistent Docker-based environment
- **Environment Parity**: Dev, staging, and production MUST use same PHP/Laravel versions
- **Configuration**: Environment-specific config MUST be in `.env`; never hardcoded

### Deployment Process

- **CI/CD Pipeline**: Automated testing, linting, and deployment
- **Zero Downtime**: Deployments MUST not cause service interruption
- **Rollback Plan**: Every deployment MUST have documented rollback procedure
- **Database Migrations**: Run migrations before deploying new code

## Governance

### Constitution Authority

This constitution supersedes all other development practices and guidelines. When conflicts arise, constitution principles take precedence.

### Amendment Process

- **Proposal**: Any team member can propose amendments via documented RFC
- **Review**: Amendments MUST be reviewed by entire development team
- **Approval**: Requires consensus from 75% of team members
- **Migration**: Approved amendments MUST include migration plan for existing code
- **Versioning**: Constitution follows semantic versioning (MAJOR.MINOR.PATCH)

### Compliance & Enforcement

- **Code Reviews**: Reviewers MUST verify constitution compliance
- **Automated Checks**: CI/CD pipeline MUST enforce testable principles
- **Exceptions**: Violations MUST be documented with justification and remediation plan
- **Regular Audits**: Quarterly review of codebase for constitution compliance

### Complexity Justification

Any deviation from YAGNI (You Aren't Gonna Need It) principles MUST be justified:

- **Document**: Why simpler alternative is insufficient
- **Approve**: Get team consensus before implementation
- **Review**: Revisit complexity quarterly to assess if still needed

**Version**: 1.0.0 | **Ratified**: 2025-10-18 | **Last Amended**: 2025-10-18
