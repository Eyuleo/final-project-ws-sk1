# Feature Specification: Student Skills Marketplace

**Feature Branch**: `001-student-skills-marketplace`  
**Created**: 2025-10-18  
**Status**: Draft  
**Input**: User description: "Build a Student Skills Marketplace platform designed to connect university students in Ethiopia with clients who need their services..."

## Clarifications

### Session 2025-10-18

- Q: Which student verification method should be used? → A: Email verification only (no university domain validation required)
- Q: Which payment gateway should be integrated? → A: Stripe and Stripe Connect for student payouts
- Q: When should escrow funds auto-release if client never reviews deliverables? → A: 7 days after student marks order complete
- Q: What commission should the platform charge on completed transactions? → A: 15% flat rate
- Q: How should disputes be resolved when client rejects work and student disagrees? → A: Admin mediation with evidence review

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Student Service Provider Registration & Profile Setup (Priority: P1)

A university student wants to join the marketplace to offer their skills and earn income. They need to create an account, verify their student status, build a professional profile showcasing their skills, education, and portfolio samples.

**Why this priority**: Without student providers, there is no marketplace. This is the foundational user journey that enables the entire platform.

**Independent Test**: Can be fully tested by registering a student account, completing profile setup with skills/portfolio, and verifying the profile is visible and searchable. Delivers value by allowing students to establish their presence on the platform.

**Acceptance Scenarios**:

1. **Given** a new student visitor, **When** they click "Register as Student Provider", **Then** they see a registration form requesting email, password, full name, university, student ID, and phone number
2. **Given** a student completes registration, **When** they submit the form with valid data, **Then** their account is created and they receive an email verification link
3. **Given** a verified student account, **When** they access profile setup, **Then** they can add skills/categories, bio, hourly rate range, portfolio samples (images/documents), and education details
4. **Given** a completed student profile, **When** they publish it, **Then** their profile becomes visible in the marketplace search and discovery features

---

### User Story 2 - Service Listing Creation & Management (Priority: P1)

A registered student provider wants to create specific service offerings (e.g., "Logo Design", "Web Development", "Academic Tutoring") with descriptions, pricing, delivery time, and requirements. They need to manage multiple service listings.

**Why this priority**: Service listings are the core product catalog. Without them, clients have nothing to browse or purchase. This is essential for MVP.

**Independent Test**: Can be tested by creating multiple service listings with different categories, prices, and details, then verifying they appear in search results and on the student's profile. Delivers immediate value by making services discoverable.

**Acceptance Scenarios**:

1. **Given** a verified student provider, **When** they click "Create Service Listing", **Then** they see a form to enter service title, category, description, pricing model (fixed/hourly), price, delivery time, and requirements
2. **Given** a student creates a service listing, **When** they add portfolio samples and FAQs, **Then** these are attached to the listing for client reference
3. **Given** a published service listing, **When** a student views their dashboard, **Then** they can see all their listings with status (active/paused/draft) and edit or delete them
4. **Given** multiple service listings, **When** a student pauses a listing, **Then** it becomes invisible to clients but remains in the student's dashboard for later reactivation

---

### User Story 3 - Client Registration & Service Discovery (Priority: P1)

A client (individual or business) wants to find and hire student providers for specific tasks. They need to register, browse/search services by category and filters, view detailed service listings and provider profiles, and compare options.

**Why this priority**: Clients are the demand side of the marketplace. Without client discovery, students cannot receive orders. This completes the core marketplace loop.

**Independent Test**: Can be tested by registering a client account, searching for services using filters (category, price range, rating), viewing service details and provider profiles, and adding services to a shortlist. Delivers value by enabling service discovery.

**Acceptance Scenarios**:

1. **Given** a new visitor, **When** they click "Register as Client", **Then** they see a registration form requesting email, password, full name, organization (optional), and phone number
2. **Given** a registered client, **When** they access the marketplace homepage, **Then** they see featured services, popular categories, and a search bar
3. **Given** a client searches for services, **When** they enter keywords or select a category, **Then** they see filtered results with service title, provider name, rating, price, and delivery time
4. **Given** a client views a service listing, **When** they click on it, **Then** they see full details including description, provider profile, portfolio samples, reviews, and an "Order Now" button

---

### User Story 4 - Order Placement & Escrow Payment (Priority: P2)

A client wants to hire a student provider by placing an order for a specific service. The client needs to submit order requirements, make a secure payment that is held in escrow, and receive confirmation that the order is in progress.

**Why this priority**: Order placement and payment are critical for monetization and trust. Escrow protects both parties. This is essential for a functional marketplace but can be implemented after basic discovery is working.

**Independent Test**: Can be tested by placing an order with custom requirements, making a payment via integrated payment gateway, verifying funds are held in escrow, and confirming the student receives an order notification. Delivers value by enabling actual transactions.

**Acceptance Scenarios**:

1. **Given** a client viewing a service listing, **When** they click "Order Now", **Then** they see an order form to specify requirements, quantity/hours, and delivery deadline
2. **Given** a client submits order requirements, **When** they proceed to payment, **Then** they see the total amount, payment options (credit card, mobile money), and escrow terms
3. **Given** a client completes payment, **When** the transaction is processed, **Then** funds are held in escrow, the student receives an order notification, and the client receives an order confirmation with tracking number
4. **Given** an order is placed, **When** the student views their orders dashboard, **Then** they see the new order with client requirements, deadline, and payment amount held in escrow

---

### User Story 5 - Order Management & Delivery (Priority: P2)

A student provider needs to manage incoming orders by accepting/declining them, communicating with clients about requirements, uploading deliverables, and marking orders as complete. Clients need to review deliverables and approve completion.

**Why this priority**: Order fulfillment is the core value delivery mechanism. Without it, orders cannot be completed and payments cannot be released. This directly follows order placement.

**Independent Test**: Can be tested by accepting an order, uploading deliverable files, marking as complete, and having the client review and approve. Delivers value by enabling service delivery and payment release.

**Acceptance Scenarios**:

1. **Given** a student receives an order, **When** they view order details, **Then** they can accept or decline the order with a reason
2. **Given** a student accepts an order, **When** they work on it, **Then** they can update order status (in progress, awaiting info, ready for review) and upload work-in-progress updates
3. **Given** a student completes work, **When** they upload final deliverables and mark as complete, **Then** the client receives a notification to review the work
4. **Given** a client reviews deliverables, **When** they approve completion, **Then** the escrow payment is released to the student's account and the order is marked as completed
5. **Given** a client is unsatisfied, **When** they request revisions, **Then** the order status changes to "revision requested" and the student can upload revised work

---

### User Story 6 - In-Platform Messaging (Priority: P2)

Students and clients need to communicate about order requirements, clarifications, revisions, and delivery details through a secure in-platform messaging system that maintains conversation history and supports file attachments.

**Why this priority**: Communication is essential for order success but can be implemented after basic order flow. It reduces reliance on external communication channels and keeps everything trackable.

**Independent Test**: Can be tested by sending messages between student and client accounts, attaching files, and verifying message history is preserved. Delivers value by centralizing communication.

**Acceptance Scenarios**:

1. **Given** an active order, **When** a client or student clicks "Message", **Then** they see a chat interface with conversation history
2. **Given** a messaging conversation, **When** a user types and sends a message, **Then** the other party receives a real-time notification and the message appears in their inbox
3. **Given** a message conversation, **When** a user attaches a file (image, document, PDF), **Then** the file is uploaded and the recipient can download it
4. **Given** multiple orders with different clients/students, **When** a user views their messages, **Then** they see a list of conversations organized by order with unread message indicators

---

### User Story 7 - Review & Rating System (Priority: P3)

After order completion, clients need to leave reviews and ratings for student providers to build credibility. Students should also be able to rate clients. Reviews should be visible on profiles and service listings to help future users make informed decisions.

**Why this priority**: Reviews build trust and credibility but are not required for initial transactions. This can be added after the core transaction flow is working.

**Independent Test**: Can be tested by completing an order, submitting a review with rating and text, and verifying it appears on the provider's profile and service listing. Delivers value by building marketplace trust.

**Acceptance Scenarios**:

1. **Given** a completed order, **When** a client views the order details, **Then** they see a "Leave Review" button
2. **Given** a client clicks "Leave Review", **When** they submit a rating (1-5 stars), written review, and optional tags (professional, responsive, quality work), **Then** the review is published on the student's profile and service listing
3. **Given** a student provider profile, **When** a visitor views it, **Then** they see average rating, total reviews, and recent review excerpts
4. **Given** a completed order, **When** a student rates a client, **Then** the rating appears on the client's profile (visible only to students considering orders)

---

### User Story 8 - Student Earnings & Withdrawal (Priority: P3)

Student providers need to track their earnings, view transaction history, and withdraw funds to their bank account or mobile money wallet. The platform should handle withdrawal requests securely and provide transparent fee structures.

**Why this priority**: While important for student satisfaction, basic earnings tracking can be manual initially. Automated withdrawals can be added after core marketplace functions are stable.

**Independent Test**: Can be tested by completing orders, accumulating earnings, requesting a withdrawal, and verifying funds are transferred to the student's account. Delivers value by enabling income realization.

**Acceptance Scenarios**:

1. **Given** a student provider, **When** they view their earnings dashboard, **Then** they see total earnings, available balance, pending balance (in escrow), and completed transactions
2. **Given** a student has available balance, **When** they click "Withdraw Funds", **Then** they see withdrawal options (bank transfer, mobile money), minimum withdrawal amount, and processing fees
3. **Given** a student submits a withdrawal request, **When** it is processed, **Then** they receive confirmation and funds are transferred within the specified timeframe (e.g., 3-5 business days)
4. **Given** a withdrawal is completed, **When** the student views transaction history, **Then** they see the withdrawal record with date, amount, fees, and status

---

### Edge Cases

- **Student Verification**: Students self-report university affiliation; no automated verification required
- **Payment Failures**: How does the system handle failed payment transactions during order placement?
- **Dispute Resolution**: Admin reviews evidence (deliverables, requirements, messages) and decides outcome
- **Inactive Accounts**: How does the platform handle students who graduate or clients who abandon orders?
- **Service Pricing**: What happens when a student changes service pricing while orders are pending?
- **Escrow Timeout**: Funds automatically release to student after 7 days if client does not review
- **Withdrawal Limits**: How does the platform handle large withdrawal requests or suspicious activity?
- **Multiple Orders**: Can a client order the same service multiple times simultaneously?
- **Service Availability**: What happens when a student is overbooked and cannot accept new orders?
- **Review Abuse**: How does the platform prevent fake reviews or review manipulation?
- **Data Privacy**: How is student and client personal information protected (GDPR/data protection compliance)?
- **Currency Handling**: Stripe handles currency conversion; platform displays prices in USD/EUR with ETB equivalent where Stripe supports it
- **Payment Methods**: Stripe payment methods available in Ethiopia (international cards primarily; mobile money support depends on Stripe availability)

## Requirements *(mandatory)*

### Functional Requirements

#### Authentication & User Management
- **FR-001**: System MUST support separate registration flows for students and clients with role-based access
- **FR-002**: System MUST send email verification links upon registration (no university domain validation required)
- **FR-003**: System MUST support password reset via email
- **FR-004**: Users MUST be able to update their profile information (bio, contact, skills, portfolio)
- **FR-005**: System MUST allow students to self-identify their university without automated verification

#### Service Listings
- **FR-006**: Student providers MUST be able to create multiple service listings with title, category, description, pricing, and delivery time
- **FR-007**: System MUST support service categorization (e.g., Design, Development, Writing, Tutoring, Marketing)
- **FR-008**: Service listings MUST support portfolio attachments (images, PDFs, documents up to 10MB per file)
- **FR-009**: Students MUST be able to set pricing models (fixed price or hourly rate)
- **FR-010**: Students MUST be able to pause, activate, or delete service listings

#### Search & Discovery
- **FR-011**: System MUST provide search functionality with filters (category, price range, rating, delivery time)
- **FR-012**: System MUST display service listings with preview information (title, provider, price, rating, thumbnail)
- **FR-013**: System MUST show detailed service view with full description, provider profile, portfolio, and reviews
- **FR-014**: System MUST feature popular services and top-rated providers on the homepage

#### Order Management
- **FR-015**: Clients MUST be able to place orders by specifying custom requirements and delivery deadline
- **FR-016**: System MUST calculate total order cost including platform fees and display before payment
- **FR-017**: Students MUST be able to accept or decline orders with optional reason
- **FR-018**: System MUST track order status (pending, in progress, revision requested, completed, cancelled)
- **FR-019**: Students MUST be able to upload deliverable files for client review
- **FR-020**: Clients MUST be able to approve completion or request revisions (up to 2 revisions per order)

#### Payment & Escrow
- **FR-021**: System MUST integrate with Stripe for payment processing and Stripe Connect for student payouts
- **FR-022**: System MUST hold client payments in escrow until order completion is approved
- **FR-023**: System MUST release escrow funds to student provider upon client approval
- **FR-024**: System MUST auto-release escrow funds to student after 7 days if client does not review deliverables
- **FR-025**: System MUST handle refunds to clients if orders are cancelled before student acceptance
- **FR-026**: System MUST charge 15% platform commission on completed transactions
- **FR-027**: System MUST support Stripe payment methods available in Ethiopia (cards, mobile money where supported)

#### Messaging
- **FR-028**: System MUST provide in-platform messaging between students and clients for active orders
- **FR-029**: System MUST support file attachments in messages (up to 5MB per file)
- **FR-030**: System MUST send email/SMS notifications for new messages
- **FR-031**: System MUST maintain message history for the duration of the order plus 30 days

#### Reviews & Ratings
- **FR-032**: Clients MUST be able to leave reviews and ratings (1-5 stars) after order completion
- **FR-033**: System MUST calculate and display average rating for each student provider
- **FR-034**: System MUST display recent reviews on service listings and provider profiles
- **FR-035**: Students MUST be able to rate clients (visible only to other students)

#### Earnings & Withdrawals
- **FR-036**: System MUST track student earnings (total, available, pending in escrow)
- **FR-037**: Students MUST be able to request payouts via Stripe Connect to their connected bank account
- **FR-038**: System MUST process payout requests according to Stripe Connect payout schedule
- **FR-039**: System MUST maintain transaction history for all payments, earnings, and withdrawals
- **FR-040**: System MUST deduct 15% commission before transferring funds to student's Stripe Connect account

#### Admin & Moderation
- **FR-041**: System MUST provide admin dashboard to monitor platform activity, users, and transactions
- **FR-042**: Admins MUST be able to handle dispute resolution by reviewing deliverables, order requirements, and message history
- **FR-043**: Admins MUST be able to decide dispute outcomes (release funds to student, refund client, or partial split)
- **FR-044**: System MUST flag suspicious activity (duplicate accounts, payment fraud, fake reviews)
- **FR-045**: System MUST notify both parties of admin dispute decisions with reasoning

### Key Entities

- **User**: Base entity with authentication credentials, role (student/client/admin), email, phone, verification status
- **StudentProfile**: Extends User with university, student ID, skills, bio, hourly rate range, portfolio, total earnings, rating
- **ClientProfile**: Extends User with organization name (optional), total orders, rating (from students)
- **ServiceListing**: Created by student, contains title, category, description, pricing model, price, delivery time, status, portfolio samples
- **Category**: Service categories (Design, Development, Writing, Tutoring, Marketing, Video Editing, etc.)
- **Order**: Links client to service listing, contains requirements, deadline, status, total amount, escrow status, deliverables
- **Message**: Links sender to recipient within order context, contains text content, attachments, timestamp
- **Review**: Links client to student for completed order, contains rating (1-5), review text, tags, timestamp
- **Transaction**: Records all financial activities (payments, escrow releases, withdrawals, refunds) with amount, type, status
- **Withdrawal**: Student withdrawal request with amount, method (bank/mobile money), account details, status, processing date

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: Students can complete registration and profile setup in under 5 minutes
- **SC-002**: Students can create a service listing with portfolio samples in under 3 minutes
- **SC-003**: Clients can find relevant services using search/filters within 30 seconds
- **SC-004**: 90% of clients successfully complete order placement and payment on first attempt
- **SC-005**: Order acceptance rate by students exceeds 80% within 24 hours of placement
- **SC-006**: Platform handles 100 concurrent users without performance degradation
- **SC-007**: Payment processing completes in under 10 seconds with 99.5% success rate
- **SC-008**: Message delivery occurs in real-time (<2 seconds) for active users
- **SC-009**: 70% of completed orders receive client reviews within 7 days
- **SC-010**: Student withdrawal requests are processed within 5 business days with 99% success rate
- **SC-011**: Platform achieves 50 active student providers and 100 completed orders within first 3 months
- **SC-012**: Average student earnings reach 5,000 ETB per month after 6 months of platform operation
- **SC-013**: Client satisfaction rate (based on reviews and repeat orders) exceeds 85%
- **SC-014**: Platform uptime exceeds 99.5% during business hours
