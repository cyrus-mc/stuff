#include <stdio.h>
#include <sys/sem.h>
#include <sys/shm.h>

#define SHMDATASIZE	1000

void *safeshmat(int shmid, const void *shmaddr, int shmflg);

int main() {
	int semid, shmid, max;
	int value = 5;
	void *shmdata;

	semid = safesemget(IPC_PRIVATE, 1, SHM_R | SHM_W);
	printf("Semaphore ID is %d\n", semid);

	shmid = safeshmget(IPC_PRIVATE, SHMDATASIZE, IPC_CREAT | SHM_R | SHM_W);
	printf("Shared memory ID is %d\n", shmid);
	shmdata = safeshmat(shmid, 0, 0);

	shmctl(shmid, IPC_RMID, NULL);	
	
	// write something to memory
	*(int *)shmdata = value;

	scanf("%d", &max);
	// delete the semaphore
	semctl(semid, 0, IPC_RMID, 0);
	return 0;
}

int safesemget(key_t key, int nsems, int semflg) {
	int retval;

	retval = semget(key, nsems, semflg);
	if (retval == -1)
		fprintf(stderr, "Error get semaphore\n");

	return retval;
}

int safeshmget(key_t key, int size, int shmflg) {
	int retval;

	retval = shmget(key, size, shmflg);
	if (retval == -1)
		fprintf(stderr, "Error getting shared memory\n");

	return retval;
}

void *safeshmat(int shmid, const void *shmaddr, int shmflg) {
	void *retval;

	retval = shmat(shmid, shmaddr, shmflg);
	if (retval == (void *) -1)
		fprintf(stderr, "Error mapping shared memory\n");

	return retval;
}
