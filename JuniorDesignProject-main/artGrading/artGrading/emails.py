from django.core.mail import EmailMessage

def sendEmail(assignment_name,filepath,recipient):
    email = EmailMessage(
        subject = assignment_name, # name of the assignment
        to = [recipient], # email address of student thing is being emailed to
    )
    email.attach_file(filepath) # the filepath relative to here, i.e. '/projects/student/project1.jpg'
    email.send(fail_silently=False)